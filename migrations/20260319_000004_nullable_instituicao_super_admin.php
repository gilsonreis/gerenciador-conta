<?php
/**
 * Migration: Torna `instituicao_id` nullable na tabela `usuarios`
 *
 * Super Admins não pertencem a nenhuma instituição específica.
 * Com NULL no campo, o AuthHelper retorna inst=0, que os repositórios
 * interpretam como "sem filtro de instituição" → acesso total.
 *
 * Idempotente: verifica o NULLABLE atual antes de alterar.
 */

/** @var PDO $db */

// Verifica se a coluna já é nullable
$check = $db->query("
    SELECT IS_NULLABLE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'usuarios'
      AND COLUMN_NAME = 'instituicao_id'
");
$col = $check->fetch(PDO::FETCH_ASSOC);

if ($col && $col['IS_NULLABLE'] === 'NO') {
    $db->exec("
        ALTER TABLE `usuarios`
        MODIFY COLUMN `instituicao_id` INT NULL
            COMMENT 'NULL = super_admin (sem vínculo com instituição)'
    ");
    echo "  - Coluna 'instituicao_id' alterada para NULLABLE em 'usuarios'.\n";
} else {
    echo "  - Coluna 'instituicao_id' já é NULLABLE ou não encontrada. Nenhuma alteração.\n";
}
