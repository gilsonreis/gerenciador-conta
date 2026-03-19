<?php
/**
 * cron/salvar_snapshots.php
 * 
 * Cron Job: Salva o snapshot do saldo real de cada conta no dia 01 de cada mês.
 * 
 * Configurar no crontab do servidor:
 *   0 0 1 * * php /caminho/completo/cron/salvar_snapshots.php >> /var/log/snapshots.log 2>&1
 * 
 * Pode ser rodado mais de uma vez sem problema (ON DUPLICATE KEY UPDATE).
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/Repositories/SnapshotRepository.php';

$db = Database::getConnection();
$snapshotRepo = new SnapshotRepository();

// Mês de referência: sempre o dia 01 do mês atual
$mesReferencia = date('Y-m-01');

echo "[" . date('Y-m-d H:i:s') . "] Iniciando snapshot para: {$mesReferencia}\n";

// Buscar saldo real atual de cada conta (mesma query do ContaRepository::saldos)
$sql = "
    SELECT 
        c.id,
        c.nome,
        c.instituicao_id,
        (c.saldo_inicial 
         + COALESCE((SELECT SUM(valor) FROM caixa_entradas WHERE conta_id = c.id), 0) 
         - COALESCE((SELECT SUM(valor - desconto) FROM parcelas WHERE conta_pagamento_id = c.id AND data_pagamento IS NOT NULL), 0)
         + COALESCE((SELECT SUM(valor) FROM transferencias WHERE conta_destino_id = c.id), 0)
         - COALESCE((SELECT SUM(valor) FROM transferencias WHERE conta_origem_id = c.id), 0)
        ) as saldo_atual_real
    FROM contas c
    ORDER BY c.instituicao_id, c.nome
";

$stmt = $db->query($sql);
$contas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$erros = 0;

foreach ($contas as $conta) {
    try {
        $snapshotRepo->salvarSnapshot(
            (int)$conta['id'],
            (float)$conta['saldo_atual_real'],
            $mesReferencia
        );
        echo "  [OK] {$conta['nome']} (ID={$conta['id']}, Inst={$conta['instituicao_id']}): R$ " . number_format($conta['saldo_atual_real'], 2, ',', '.') . "\n";
        $total++;
    } catch (Exception $e) {
        echo "  [ERRO] {$conta['nome']}: " . $e->getMessage() . "\n";
        $erros++;
    }
}

echo "\n[CONCLUÍDO] {$total} snapshots salvos, {$erros} erros. Referência: {$mesReferencia}\n";
