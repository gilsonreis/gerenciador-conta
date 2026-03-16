<?php
/**
 * Script de Execução via Cron para notificar contas a vencer.
 * O ideal é programá-lo para rodar diariamente às 07:00 ou 08:00 AM.
 * Exemplo de cron: 0 8 * * * /usr/bin/php /caminho/para/cron/notificar_vencimentos.php >> /caminho/logs/cron_vencimentos.log 2>&1
 */

// Trava de Segurança: Apenas CLI
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Acesso negado. Este script só pode ser executado via linha de comando (CLI).\n");
}

echo "========================================================\n";
echo "Iniciando Rotina de Notificação de Vencimentos - " . date('Y-m-d H:i:s') . "\n";
echo "========================================================\n\n";

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Database.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

try {
    $db = Database::getConnection();
} catch (Exception $e) {
    exit("Falha crítica: Não foi possível conectar ao banco de dados: " . $e->getMessage() . "\n");
}
// Importação das Variáveis Nativas
$env = require __DIR__ . '/../config/env.php';

// Configuração STMTP DSN (Symfony)
$smtpUser = $env['SMTP_USER'];
$smtpPass = $env['SMTP_PASS'];
$smtpHost = $env['SMTP_HOST']; 
$smtpPort = $env['SMTP_PORT'];

// Trocamos o rawulrencode que protege senhas com caracteres especiais na URL de DSN.
// Adicionado ?verify_peer=0 para não travar na divergência de nome do Certificado SSL do servidor compartilhado
$dsn = sprintf('smtp://%s:%s@%s:%s?verify_peer=0', rawurlencode($smtpUser), rawurlencode($smtpPass), $smtpHost, $smtpPort);

try {
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);
} catch (\Throwable $e) {
    exit("Falha crítica: Não foi possível instanciar o Transportador DSN do Mailer: " . $e->getMessage() . "\n");
}


echo ">> Buscando vencimentos consolidados para Hoje, 3, 5 ou 10 dias...\n";

// 1. Consulta Única: Busca todas as parcelas alvo e agrupa por DATEDIFF
$sql = "
    SELECT 
        p.id as parcela_id,
        p.numero_parcela,
        p.total_parcelas,
        p.valor,
        p.data_vencimento,
        DATEDIFF(p.data_vencimento, CURDATE()) as dias_restantes,
        l.descricao as titulo_despesa,
        u.id as usuario_id,
        u.nome as usuario_nome,
        u.email as usuario_email,
        c.nome as categoria_nome
    FROM parcelas p
    JOIN lancamentos l ON p.lancamento_id = l.id
    LEFT JOIN categorias c ON l.categoria_id = c.id
    JOIN usuarios u ON u.instituicao_id = l.instituicao_id
    WHERE 
        p.data_pagamento IS NULL 
        AND DATEDIFF(p.data_vencimento, CURDATE()) IN (0, 3, 5, 10)
    ORDER BY dias_restantes ASC
";

$stmt = $db->query($sql);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($resultados)) {
    echo "   Nenhuma parcela se encaixa nas regras de alerta para hoje.\n\n";
} else {
    // 2. Agrupamento de Dados (Digest Arrays)
    // Estrutura: $alertasPorEmail[email] = [ 'nome' => '...', 'vencimentos' => [ 0 => [...], 3 => [...] ] ]
    $alertasPorEmail = [];
    $totalContasEncontradas = 0;
    
    foreach ($resultados as $linha) {
        $email = $linha['usuario_email'];
        $diasRestantes = (int)$linha['dias_restantes'];
        
        if (!isset($alertasPorEmail[$email])) {
            $alertasPorEmail[$email] = [
                'nome' => $linha['usuario_nome'],
                'vencimentos' => []
            ];
        }
        
        if (!isset($alertasPorEmail[$email]['vencimentos'][$diasRestantes])) {
            $alertasPorEmail[$email]['vencimentos'][$diasRestantes] = [];
        }
        
        $alertasPorEmail[$email]['vencimentos'][$diasRestantes][] = $linha;
        $totalContasEncontradas++;
    }
    
    echo "   Encontradas {$totalContasEncontradas} conta(s) para notificar.\n\n";
    
    $totalEmailsEnviados = 0;

    // 3. Montagem e Envio dos E-mails
    foreach ($alertasPorEmail as $email => $dadosUsuario) {
        $quantidadeAlertasNesteEmail = 0;
        foreach($dadosUsuario['vencimentos'] as $grupo) {
            $quantidadeAlertasNesteEmail += count($grupo);
        }
        
        $assunto = "Seu Resumo Financeiro: Contas a vencer";
        
        // Construção do Corpo HTML Principal
        $corpoHtml = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; background-color: #f8fafc; }
                .urgente-box { border-left: 4px solid #dc2626; padding-left: 15px; margin-bottom: 25px; }
                .atencao-box { border-left: 4px solid #f59e0b; padding-left: 15px; margin-bottom: 25px; }
                .normal-box { border-left: 4px solid #3b82f6; padding-left: 15px; margin-bottom: 25px; }
                h3 { margin-top: 0; margin-bottom: 10px; }
                .urgente-text { color: #dc2626; }
                .atencao-text { color: #d97706; }
                .normal-text { color: #2563eb; }
                .lista-contas { background: white; padding: 15px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                ul { list-style-type: none; padding: 0; margin: 0; }
                li { margin-bottom: 15px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 15px; }
                li:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
                .valor { font-weight: bold; color: #0f172a; font-size: 1.1em; }
                .cat { display: inline-block; background: #e2e8f0; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-bottom: 5px; }
                .titulo_conta { font-size: 1.1em; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Olá <strong>{$dadosUsuario['nome']}</strong>!</h2>
                <p>Este é o seu resumo financeiro automático. Por favor, confira abaixo os pagamentos que estão aguardando acerto:</p>
                <br/>
        ";

        // Ordena para exibir sempre os mais urgentes primeiro (0, depois 3, 5, 10...)
        ksort($dadosUsuario['vencimentos']);

        foreach ($dadosUsuario['vencimentos'] as $dias => $parcelas) {
            $blocoCss = 'normal';
            $tituloBloco = "Vencendo em {$dias} dias";
            
            if ($dias === 0) {
                $blocoCss = 'urgente';
                $tituloBloco = "🚨 Vencendo Hoje!";
            } elseif ($dias <= 5) {
                $blocoCss = 'atencao';
                $tituloBloco = "⚠️ Vencendo em {$dias} dias";
            }
            
            $corpoHtml .= "
                <div class='{$blocoCss}-box'>
                    <h3 class='{$blocoCss}-text'>{$tituloBloco}</h3>
                    <div class='lista-contas'>
                        <ul>
            ";
            
            foreach ($parcelas as $p) {
                $valorFormatado = 'R$ ' . number_format($p['valor'], 2, ',', '.');
                $parcelaInfo = ($p['total_parcelas'] > 1) ? "<em style='color: #64748b; font-size: 0.9em;'>(Parc. {$p['numero_parcela']}/{$p['total_parcelas']})</em>" : "<em style='color: #64748b; font-size: 0.9em;'>(Fixa/Única)</em>";
                $categoriaHtml = $p['categoria_nome'] ? "<div class='cat'>{$p['categoria_nome']}</div><br/>" : "";
                
                $corpoHtml .= "
                            <li>
                                {$categoriaHtml}
                                <strong class='titulo_conta'>{$p['titulo_despesa']}</strong> {$parcelaInfo} <br/>
                                <div>Valor a Pagar: <span class='valor'>{$valorFormatado}</span></div>
                            </li>
                ";
            }
            
            $corpoHtml .= "
                        </ul>
                    </div>
                </div>
            ";
        }

        $corpoHtml .= "
                <p style='margin-top: 30px;'>Acesse o painel para confirmar o pagamento ou ver mais detalhes.</p>
                <p><small style='color: #94a3b8;'>Este é um alerta automático gerado pelo sistema.</small></p>
            </div>
        </body>
        </html>
        ";

        // Envio via Symfony Mailer com Error Handling
        try {
            $emailToSend = (new Email())
                ->from('sender@simplifysoftwares.com.br')
                ->to($email)
                ->subject($assunto)
                ->html($corpoHtml);

            $mailer->send($emailToSend);
            
            $totalEmailsEnviados++;
            echo "   [✓] Enviado 1 e-mail de resumo para {$email} contendo {$quantidadeAlertasNesteEmail} alerta(s).\n";
        } catch (\Throwable $e) {
            echo "   [✗] Erro ao enviar e-mail de resumo para {$email}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    echo ">> Finalizado. Disparados {$totalEmailsEnviados} e-mail(s) no total.\n";
}

echo "========================================================\n";
echo "Fim da Execução - " . date('Y-m-d H:i:s') . "\n";
echo "========================================================\n";
