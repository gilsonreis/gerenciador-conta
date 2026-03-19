<?php
/**
 * Migration: Seed de Dados Iniciais
 * Insere a instituição, categorias e usuários padrão.
 * Idempotente: usa INSERT IGNORE ou ON DUPLICATE KEY UPDATE.
 */

/** @var PDO $db */

// Instituição
$db->exec("
    INSERT IGNORE INTO `instituicoes` (`id`, `nome`) VALUES (1, 'Família Reis');
");
echo "  - Instituição 'Família Reis' OK\n";

// Categorias
$categorias = [
    [1,  1, 'Alimentação'],
    [2,  1, 'Moradia'],
    [3,  1, 'Transporte'],
    [8,  1, 'Lazer e Streaming'],
    [9,  1, 'Educação'],
    [10, 1, 'Cartão de Crédito'],
    [11, 1, 'Delivery'],
    [12, 1, 'Compras avulsas'],
    [13, 1, 'Ferramenta de Trabalho / IA'],
    [14, 1, 'Imposto'],
    [15, 1, 'Farmácia / Saúde'],
    [16, 1, 'Internet & Celular'],
];

$stmt = $db->prepare("INSERT IGNORE INTO `categorias` (`id`, `instituicao_id`, `nome`) VALUES (:id, :inst, :nome)");
foreach ($categorias as [$id, $inst, $nome]) {
    $stmt->execute(['id' => $id, 'inst' => $inst, 'nome' => $nome]);
}
echo "  - " . count($categorias) . " categorias inseridas OK\n";

// Usuários (senhas já hashadas com bcrypt)
$usuarios = [
    [1, 1, 'Admin',         'admin@familia.com',              '$2y$12$xBNshqh7INkgD22ntHf0Ku5J3clVvXcjgrpFn2kUQez3MAHwllhky', 1],
    [4, 1, 'Gilson Reis',   'gilsonc.reis@gmail.com',         '$2y$12$xBNshqh7INkgD22ntHf0Ku5J3clVvXcjgrpFn2kUQez3MAHwllhky', 1],
    [5, 1, 'Amanda Muniz',  'amandamunizdasilva@hotmail.com', '$2y$12$sfOKt8UYj73IOIMqdZgahuGLvbAx8NJ2nQXbNviKuJm92JoD0S6l6',  1],
];

$stmt = $db->prepare("
    INSERT IGNORE INTO `usuarios` (`id`, `instituicao_id`, `nome`, `email`, `senha`, `recebe_alertas`)
    VALUES (:id, :inst, :nome, :email, :senha, :alertas)
");
foreach ($usuarios as [$id, $inst, $nome, $email, $senha, $alertas]) {
    $stmt->execute(['id' => $id, 'inst' => $inst, 'nome' => $nome, 'email' => $email, 'senha' => $senha, 'alertas' => $alertas]);
}
echo "  - " . count($usuarios) . " usuários inseridos OK\n";
