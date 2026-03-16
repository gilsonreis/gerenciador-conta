<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CategoriaRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new CategoriaRepository();
$categoria = $repo->buscar(AuthHelper::getInstituicaoId(), $id);

if ($categoria) {
    echo json_encode(['sucesso' => true, 'dados' => $categoria]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Categoria não encontrada']);
}
