<?php
/**
 * Migration: Tabela de Snapshots de Saldos Mensais
 * Cria a tabela snapshots_saldos para o sistema de Marco Zero Mensal.
 * Idempotente: usa CREATE TABLE IF NOT EXISTS.
 */

/** @var PDO $db */

$db->exec("
    CREATE TABLE IF NOT EXISTS `snapshots_saldos` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `conta_id` INT(11) NOT NULL,
        `valor_abertura` DECIMAL(15,2) NOT NULL,
        `mes_referencia` DATE NOT NULL,
        `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_snapshot` (`conta_id`, `mes_referencia`),
        CONSTRAINT `snapshots_saldos_ibfk_1` FOREIGN KEY (`conta_id`) REFERENCES `contas` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'snapshots_saldos' OK\n";
