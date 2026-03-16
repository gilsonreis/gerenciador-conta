<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/InstituicaoRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new InstituicaoRepository();
// Pass user ID to restrict access
$instituicao = $repo->buscar(AuthHelper::getUsuarioId(), $id);

if ($instituicao) {
    echo json_encode(['sucesso' => true, 'dados' => $instituicao]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Instituição não encontrada ou acesso negado.']);
}
