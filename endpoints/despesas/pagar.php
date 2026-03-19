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

// Data de pagamento: usa o que veio do formulário ou assume hoje
$dataPagamento = !empty($_POST['data_pagamento']) ? $_POST['data_pagamento'] : date('Y-m-d');
$contaPagamentoId = filter_input(INPUT_POST, 'conta_pagamento_id', FILTER_VALIDATE_INT) ?: null;

$repo = new ParcelaRepository();

// Busca estado atual da parcela para determinar toggle
require_once __DIR__ . '/../../config/Database.php';
$dbConn = Database::getConnection();
$check = $dbConn->prepare("SELECT data_pagamento FROM parcelas WHERE id = :id");
$check->execute(['id' => $id]);
$parcela = $check->fetch();

if ($parcela && $parcela['data_pagamento'] !== null) {
    // Já está paga: desfaz pagamento (toggle off → NULL)
    if ($repo->registrarPagamento(AuthHelper::getInstituicaoId(), $id, null, 0, null)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Pagamento desfeito com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao desfazer pagamento.']);
    }
} else {
    // Não está paga: registra o pagamento com a data informada
    if ($repo->registrarPagamento(AuthHelper::getInstituicaoId(), $id, $dataPagamento, 0, $contaPagamentoId)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Parcela marcada como paga.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao registrar pagamento.']);
    }
}
