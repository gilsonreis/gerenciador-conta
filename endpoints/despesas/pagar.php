<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ParcelaRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new ParcelaRepository();
if ($repo->alternarStatus(AuthHelper::getInstituicaoId(), $id)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Status da parcela alterado com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao alterar o status da parcela']);
}
