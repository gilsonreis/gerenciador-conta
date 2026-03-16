<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new CaixaRepository();
$caixa = $repo->buscar(AuthHelper::getInstituicaoId(), $id);

if ($caixa) {
    $caixa['valor'] = number_format((float)$caixa['valor'], 2, ',', '.');
    echo json_encode(['sucesso' => true, 'dados' => $caixa]);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Entrada não encontrada']);
}
