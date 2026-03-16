<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ParcelaRepository.php';

AuthHelper::requireLogin();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$valor_raw = filter_input(INPUT_POST, 'valor');
$data_vencimento = filter_input(INPUT_POST, 'data_vencimento');

if (!$id || empty($valor_raw) || empty($data_vencimento)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Faltam dados obrigatórios para atualizar a parcela.']);
    exit;
}

// Tratar valor para Float (do formato brasileiro para o banco de dados)
$valor = (float)str_replace(',', '.', str_replace('.', '', $valor_raw));

$repo = new ParcelaRepository();
if ($repo->atualizarIndividual(AuthHelper::getInstituicaoId(), $id, $valor, $data_vencimento)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Amortização/Alteração salva com sucesso!']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao salvar a parcela individualmente.']);
}

