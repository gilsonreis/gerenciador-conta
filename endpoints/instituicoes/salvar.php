<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/InstituicaoRepository.php';

AuthHelper::requireLogin();

$nome = trim($_POST['nome'] ?? '');
if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome da instituição é obrigatório']);
    exit;
}

$dados = [
    'id' => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null,
    'nome' => $nome
];

$repo = new InstituicaoRepository();

// Pass the user ID for security validation inside the repository
if ($repo->salvar(AuthHelper::getUsuarioId(), $dados)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Instituição salva com sucesso']);
} else {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado. Você não tem permissão para alterar esta instituição ou houve uma falha.']);
}
