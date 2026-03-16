<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/UsuarioRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new UsuarioRepository();
$usuario = $repo->buscar(AuthHelper::getInstituicaoId(), $id);

if ($usuario) {
    echo json_encode(['sucesso' => true, 'dados' => $usuario]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Usuário não encontrado']);
}
