<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';

AuthHelper::requireLogin();

// Observação: Excluímos pelo lancamento_id pois apagaremos toda a cadeia dessa despesa
$lancamento_id = filter_input(INPUT_POST, 'lancamento_id', FILTER_VALIDATE_INT);
if (!$lancamento_id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id do Lançamento inválido']);
    exit;
}

$repo = new LancamentoRepository();
if ($repo->excluir(AuthHelper::getInstituicaoId(), $lancamento_id)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Despesa e todas as suas parcelas foram excluídas.']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao excluir a despesa']);
}
