<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ParcelaRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$isEstorno = filter_input(INPUT_POST, 'estorno', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new ParcelaRepository();

if ($isEstorno) {
    if ($repo->registrarPagamento(AuthHelper::getInstituicaoId(), $id, null, 0, null)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Pagamento estornado com sucesso!']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao estornar pagamento.']);
    }
} else {
    $dataPagamento = filter_input(INPUT_POST, 'data_pagamento', FILTER_SANITIZE_SPECIAL_CHARS);
    $contaPagamentoId = filter_input(INPUT_POST, 'conta_pagamento_id', FILTER_VALIDATE_INT);
    
    // Tratamento seguro de valor monetario recebido formatado no BR
    $descontoStr = filter_input(INPUT_POST, 'desconto', FILTER_SANITIZE_SPECIAL_CHARS) ?? '0';
    $desconto = (float)str_replace(',', '.', str_replace('.', '', $descontoStr));
    
    if (empty($dataPagamento) || empty($contaPagamentoId)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Data de pagamento e Carteira são obrigatórias.']);
        exit;
    }

    if ($repo->registrarPagamento(AuthHelper::getInstituicaoId(), $id, $dataPagamento, $desconto, $contaPagamentoId)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Pagamento da parcela registrado!']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao processar o pagamento da parcela.']);
    }
}
