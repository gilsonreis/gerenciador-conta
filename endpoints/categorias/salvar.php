<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CategoriaRepository.php';

AuthHelper::requireLogin();

$nome = trim($_POST['nome'] ?? '');
if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome da categoria é obrigatório']);
    exit;
}

$dados = [
    'id' => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null,
    'nome' => $nome
];

$repo = new CategoriaRepository();
if ($repo->salvar(AuthHelper::getInstituicaoId(), $dados)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Categoria salva com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao salvar categoria']);
}
