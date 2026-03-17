<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/TransferenciaRepository.php';

AuthHelper::requireLogin();

$repo = new TransferenciaRepository();
$dados = $_POST;

if (empty($dados['conta_origem_id']) || empty($dados['conta_destino_id']) || empty($dados['valor']) || empty($dados['data_transferencia'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

if ($dados['conta_origem_id'] == $dados['conta_destino_id']) {
    http_response_code(400);
    echo json_encode(['erro' => 'A conta de origem não pode ser igual à de destino.']);
    exit;
}

try {
    if ($repo->salvar(AuthHelper::getInstituicaoId(), $dados)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Transferência realizada com sucesso!']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao processar transferência.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno.', 'detalhe' => $e->getMessage()]);
}
