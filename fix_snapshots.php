<?php
/**
 * fix_snapshots.php
 * Script de migração: cria a tabela snapshots_saldos e insere os snapshots
 * manuais de Março/2026 para Bradesco e Itaú.
 * 
 * Execute UMA VEZ via terminal: php fix_snapshots.php
 */
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();

    // 1. Criar tabela snapshots_saldos
    $db->exec("
        CREATE TABLE IF NOT EXISTS snapshots_saldos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            conta_id INT NOT NULL,
            valor_abertura DECIMAL(15,2) NOT NULL,
            mes_referencia DATE NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_snapshot (conta_id, mes_referencia),
            FOREIGN KEY (conta_id) REFERENCES contas(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "[OK] Tabela snapshots_saldos criada (ou já existia).\n";

    // 2. Buscar IDs das contas por nome (Bradesco e Itaú)
    $contas = [
        'bradesco' => ['nome_like' => '%Bradesco%', 'valor' => 4364.78],
        'itau'     => ['nome_like' => '%Ita%',      'valor' => 839.90],
    ];

    $mes = '2026-03-01';

    foreach ($contas as $chave => $info) {
        $stmt = $db->prepare("SELECT id, nome FROM contas WHERE nome LIKE :nome LIMIT 1");
        $stmt->execute(['nome' => $info['nome_like']]);
        $conta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$conta) {
            echo "[AVISO] Conta '{$chave}' não encontrada com padrão '{$info['nome_like']}'. Pulando.\n";
            continue;
        }

        $stmt2 = $db->prepare("
            INSERT INTO snapshots_saldos (conta_id, valor_abertura, mes_referencia)
            VALUES (:conta_id, :valor, :mes)
            ON DUPLICATE KEY UPDATE valor_abertura = VALUES(valor_abertura)
        ");
        $stmt2->execute([
            'conta_id' => $conta['id'],
            'valor'    => $info['valor'],
            'mes'      => $mes,
        ]);

        echo "[OK] Snapshot de Março/2026 salvo: {$conta['nome']} (ID={$conta['id']}) = R$ " . number_format($info['valor'], 2, ',', '.') . "\n";
    }

    echo "\n[CONCLUÍDO] Migration de snapshots finalizada com sucesso.\n";

} catch (Exception $e) {
    echo "[ERRO] " . $e->getMessage() . "\n";
}
