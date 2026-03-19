<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('write');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new ContaRepository();
try {
    if ($repo->excluir(AuthHelper::getInstituicaoId(), $id)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Conta excluída com sucesso!']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao excluir a conta.']);
    }
} catch (PDOException $e) {
    if($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(['erro' => 'Houve uma restrição no Banco de Dados. Ação cancelada.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha interna ao remover.']);
    }
}
