<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/TransferenciaRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('transferencias');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID inválido.']);
    exit;
}

$repo = new TransferenciaRepository();
if ($repo->excluir(AuthHelper::getInstituicaoId(), $id)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Transferência excluída e saldo estornado.']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao excluir transferência.']);
}
