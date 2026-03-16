<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';

AuthHelper::requireLogin();

$contaId = filter_input(INPUT_POST, 'conta_id', FILTER_VALIDATE_INT);
$valor_raw = trim($_POST['valor'] ?? '');
$dataEntrada = trim($_POST['data_entrada'] ?? '');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;

if (!$contaId || empty($valor_raw) || empty($dataEntrada)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Todos os campos são obrigatórios.']);
    exit;
}

$valor = (float)str_replace(',', '.', str_replace('.', '', $valor_raw));

$dados = [
    'id' => $id,
    'conta_id' => $contaId,
    'valor' => $valor,
    'data_entrada' => $dataEntrada
];

$repo = new CaixaRepository();
if ($repo->salvar(AuthHelper::getInstituicaoId(), AuthHelper::getUsuarioId(), $dados)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Entrada salva com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao salvar a entrada']);
}
