<?php
/**
 * Migration: Schema Inicial
 * Cria todas as tabelas base do sistema.
 * Idempotente: usa CREATE TABLE IF NOT EXISTS.
 */

// $db é injetado pelo executar_migrations.php
/** @var PDO $db */

$db->exec("
    CREATE TABLE IF NOT EXISTS `instituicoes` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nome` VARCHAR(100) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'instituicoes' OK\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS `usuarios` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `instituicao_id` INT(11) NOT NULL,
        `nome` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `senha` VARCHAR(255) NOT NULL,
        `recebe_alertas` TINYINT(1) DEFAULT 1,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`),
        KEY `fk_usuario_instituicao` (`instituicao_id`),
        CONSTRAINT `fk_usuario_instituicao` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'usuarios' OK\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS `contas` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `instituicao_id` INT(11) NOT NULL,
        `nome` VARCHAR(100) NOT NULL,
        `saldo_inicial` DECIMAL(10,2) DEFAULT 0.00,
        `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        KEY `fk_conta_instituicao` (`instituicao_id`),
        CONSTRAINT `fk_conta_instituicao` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'contas' OK\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS `categorias` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `instituicao_id` INT(11) NOT NULL,
        `nome` VARCHAR(50) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `fk_categoria_instituicao` (`instituicao_id`),
        CONSTRAINT `fk_categoria_instituicao` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'categorias' OK\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS `lancamentos` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `instituicao_id` INT(11) NOT NULL,
        `usuario_id` INT(11) NOT NULL,
        `categoria_id` INT(11) NOT NULL,
        `descricao` VARCHAR(255) NOT NULL,
        `conta_fixa` TINYINT(1) DEFAULT 0,
        `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        KEY `fk_lanc_instituicao` (`instituicao_id`),
        KEY `fk_lanc_categoria` (`categoria_id`),
        CONSTRAINT `fk_lanc_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
        CONSTRAINT `fk_lanc_instituicao` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'lancamentos' OK\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS `parcelas` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `lancamento_id` INT(11) NOT NULL,
        `numero_parcela` INT(11) DEFAULT 1,
        `total_parcelas` INT(11) DEFAULT 1,
        `valor` DECIMAL(10,2) NOT NULL,
        `desconto` DECIMAL(10,2) DEFAULT 0.00,
        `conta_pagamento_id` INT(11) DEFAULT NULL,
        `data_vencimento` DATE NOT NULL,
        `data_pagamento` DATE DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `fk_parcela_lancamento` (`lancamento_id`),
        KEY `fk_parcela_conta` (`conta_pagamento_id`),
        CONSTRAINT `fk_parcela_conta` FOREIGN KEY (`conta_pagamento_id`) REFERENCES `contas` (`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_parcela_lancamento` FOREIGN KEY (`lancamento_id`) REFERENCES `lancamentos` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'parcelas' OK\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS `caixa_entradas` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `instituicao_id` INT(11) NOT NULL,
        `usuario_id` INT(11) NOT NULL,
        `conta_id` INT(11) DEFAULT NULL,
        `descricao` VARCHAR(255) DEFAULT NULL,
        `valor` DECIMAL(10,2) NOT NULL,
        `data_entrada` DATE NOT NULL,
        `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
        `status` ENUM('confirmado','pendente') NOT NULL DEFAULT 'confirmado',
        PRIMARY KEY (`id`),
        KEY `fk_caixa_instituicao` (`instituicao_id`),
        KEY `fk_caixa_conta` (`conta_id`),
        CONSTRAINT `fk_caixa_instituicao` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_caixa_conta` FOREIGN KEY (`conta_id`) REFERENCES `contas` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'caixa_entradas' OK\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS `transferencias` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `instituicao_id` INT(11) NOT NULL,
        `conta_origem_id` INT(11) NOT NULL,
        `conta_destino_id` INT(11) NOT NULL,
        `valor` DECIMAL(10,2) NOT NULL,
        `data_transferencia` DATE NOT NULL,
        `descricao` VARCHAR(255) DEFAULT 'Transferência entre contas',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        KEY `fk_transf_instituicao` (`instituicao_id`),
        KEY `fk_transf_origem` (`conta_origem_id`),
        KEY `fk_transf_destino` (`conta_destino_id`),
        CONSTRAINT `fk_transf_destino` FOREIGN KEY (`conta_destino_id`) REFERENCES `contas` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_transf_instituicao` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_transf_origem` FOREIGN KEY (`conta_origem_id`) REFERENCES `contas` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - Tabela 'transferencias' OK\n";
