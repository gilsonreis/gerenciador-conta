<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ParcelaRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('write');

$lancamentoId = filter_input(INPUT_POST, 'lancamento_id', FILTER_VALIDATE_INT);
$dataPagamento = filter_input(INPUT_POST, 'data_pagamento', FILTER_SANITIZE_SPECIAL_CHARS);
$contaPagamentoId = filter_input(INPUT_POST, 'conta_pagamento_id', FILTER_VALIDATE_INT);
$descontoStr = filter_input(INPUT_POST, 'desconto', FILTER_SANITIZE_SPECIAL_CHARS) ?? '0';
$desconto = (float)str_replace(',', '.', str_replace('.', '', $descontoStr));

if (!$lancamentoId) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID do lançamento inválido']);
    exit;
}

if (empty($dataPagamento) || empty($contaPagamentoId)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Data de pagamento e Carteira são obrigatórias.']);
    exit;
}

$db = Database::getConnection();
$sql = "SELECT id FROM parcelas WHERE lancamento_id = :id AND data_pagamento IS NULL ORDER BY data_vencimento ASC LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute(['id' => $lancamentoId]);
$parcela = $stmt->fetch();

if (!$parcela) {
    http_response_code(404);
    echo json_encode(['erro' => 'Nenhuma parcela pendente encontrada para este lançamento.']);
    exit;
}

$repo = new ParcelaRepository();
if ($repo->registrarPagamento(AuthHelper::getInstituicaoId(), $parcela['id'], $dataPagamento, $desconto, $contaPagamentoId)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Pagamento da próxima parcela registrado!']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao processar o pagamento.']);
}
