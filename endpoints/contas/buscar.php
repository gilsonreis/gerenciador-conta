<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new ContaRepository();
$conta = $repo->buscar(AuthHelper::getInstituicaoId(), $id);

if ($conta) {
    $conta['saldo_inicial'] = number_format((float)$conta['saldo_inicial'], 2, ',', '.');
    echo json_encode(['sucesso' => true, 'dados' => $conta]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Conta não encontrada']);
}
