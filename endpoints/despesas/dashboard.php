<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();

$mesAno = $_GET['mes'] ?? date('Y-m');

// 1. Soma dos Saldos Reais de todas as contas (Poder de Fogo Atual)
$repoConta = new ContaRepository();
$saldosContas = $repoConta->saldos(AuthHelper::getInstituicaoId());
$somaSaldosReais = 0;
foreach ($saldosContas as $c) {
    $somaSaldosReais += (float)($c['saldo_atual_real'] ?? 0);
}

// 2. Entradas do Mês
$repoCaixa = new CaixaRepository();
$entradasMes = (float)$repoCaixa->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);

// 3. Capital Disponível (Saldo nas Contas + Entradas do Mês)
$capitalDisponivel = $somaSaldosReais + $entradasMes;

// 4. Saídas do Mês (Previstas/Pagas)
$repoLancamento = new LancamentoRepository();
$resumoDespesas = $repoLancamento->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);
$saidasMes = (float)($resumoDespesas['total_saidas'] ?? 0);
$custoVida = (float)($resumoDespesas['custo_vida'] ?? 0);

// 5. Projeção de Sobra (Capital - Saídas)
$projecaoSobra = $capitalDisponivel - $saidasMes;

echo json_encode([
    'sucesso' => true,
    'capital_disponivel' => $capitalDisponivel,
    'capital_disponivel_formatado' => 'R$ ' . number_format($capitalDisponivel, 2, ',', '.'),
    'saidas' => $saidasMes,
    'saidas_formatado' => 'R$ ' . number_format($saidasMes, 2, ',', '.'),
    'projecao_sobra' => $projecaoSobra,
    'projecao_sobra_formatado' => 'R$ ' . number_format($projecaoSobra, 2, ',', '.'),
    'custovida' => $custoVida,
    'custovida_formatado' => 'R$ ' . number_format($custoVida, 2, ',', '.')
]);
