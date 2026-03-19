<?php
/**
 * Migration: Adiciona coluna `role` à tabela `usuarios`
 *
 * Implementa a fundação do RBAC (Role-Based Access Control).
 * Roles disponíveis: super_admin, admin, manager, reader.
 * Default: 'admin' — garante que todos os usuários existentes
 * continuem com acesso completo sem interrupção.
 *
 * Idempotente: usa IF NOT EXISTS via SHOW COLUMNS antes do ALTER.
 */

/** @var PDO $db */

// Verifica se a coluna já existe antes de tentar adicionar
$check = $db->query("SHOW COLUMNS FROM `usuarios` LIKE 'role'");
if ($check->rowCount() === 0) {
    $db->exec("
        ALTER TABLE `usuarios`
        ADD COLUMN `role` ENUM('super_admin', 'admin', 'manager', 'reader')
            NOT NULL
            DEFAULT 'admin'
            COMMENT 'Perfil de acesso RBAC do usuário'
            AFTER `email`
    ");
    echo "  - Coluna 'role' adicionada à tabela 'usuarios' (default: admin)\n";
} else {
    echo "  - Coluna 'role' já existe em 'usuarios'. Nenhuma alteração necessária.\n";
}
