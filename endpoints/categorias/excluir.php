<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CategoriaRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new CategoriaRepository();

try {
    if ($repo->excluir(AuthHelper::getInstituicaoId(), $id)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Categoria excluída com sucesso']);
    } else {
        http_response_code(400);
        echo json_encode(['erro' => 'Falha ao excluir categoria']);
    }
} catch (PDOException $e) {
    http_response_code(409);
    echo json_encode(['erro' => 'Não é possível excluir esta categoria pois ela está vinculada a um ou mais lançamentos.']);
}
