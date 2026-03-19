<?php
/**
 * Migration: Snapshots Manuais de Marco Zero - Março/2026
 * 
 * Insere os saldos de abertura manuais de Março/2026 para as contas
 * Bradesco (R$ 4.364,78) e Itaú (R$ 839,90), conforme registrado pelo usuário.
 * 
 * Idempotente: usa ON DUPLICATE KEY UPDATE.
 */

/** @var PDO $db */

$mes = '2026-03-01';

$snapshots = [
    ['nome_like' => '%Bradesco%', 'valor' => 4364.78],
    ['nome_like' => '%Ita%',      'valor' => 839.90],
];

$stmtBusca = $db->prepare("SELECT id, nome FROM contas WHERE nome LIKE :nome LIMIT 1");
$stmtInsert = $db->prepare("
    INSERT INTO snapshots_saldos (conta_id, valor_abertura, mes_referencia)
    VALUES (:conta_id, :valor, :mes)
    ON DUPLICATE KEY UPDATE valor_abertura = VALUES(valor_abertura)
");

foreach ($snapshots as $snap) {
    $stmtBusca->execute(['nome' => $snap['nome_like']]);
    $conta = $stmtBusca->fetch(PDO::FETCH_ASSOC);

    if (!$conta) {
        echo "  [AVISO] Conta com padrão '{$snap['nome_like']}' não encontrada. Pulando.\n";
        continue;
    }

    $stmtInsert->execute([
        'conta_id' => $conta['id'],
        'valor'    => $snap['valor'],
        'mes'      => $mes,
    ]);

    echo "  - Snapshot {$conta['nome']} = R$ " . number_format($snap['valor'], 2, ',', '.') . " OK\n";
}
