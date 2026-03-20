<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/TransferenciaRepository.php';
require_once __DIR__ . '/../../src/Services/BalanceService.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('transferencias');

// Super admin escolhe a instituição; demais usam a sessão
$isSuperAdmin = AuthHelper::getInstituicaoId() === 0;
if ($isSuperAdmin) {
    $instituicaoId = filter_input(INPUT_POST, 'instituicao_id', FILTER_VALIDATE_INT) ?: 0;
    if (!$instituicaoId) {
        http_response_code(400);
        echo json_encode(['erro' => 'Selecione uma Instituição.']);
        exit;
    }
} else {
    $instituicaoId = AuthHelper::getInstituicaoIdReal();
}

$repo  = new TransferenciaRepository();
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

// Valor a transferir
$valorTransferencia = (float)str_replace(',', '.', str_replace('.', '', $dados['valor']));

// Validação de saldo (ALTA PRIORIDADE): impede saldo negativo fictício
$mesAno = date('Y-m', strtotime($dados['data_transferencia']));
$balanceService = new BalanceService();
$saldoOrigem = $balanceService->getSaldoAtual((int)$dados['conta_origem_id'], $mesAno);

if ($saldoOrigem < $valorTransferencia) {
    http_response_code(422);
    echo json_encode([
        'erro'             => 'Saldo insuficiente na conta de origem.',
        'saldo_disponivel' => $saldoOrigem,
        'saldo_formatado'  => 'R$ ' . number_format($saldoOrigem, 2, ',', '.'),
        'valor_solicitado' => 'R$ ' . number_format($valorTransferencia, 2, ',', '.'),
    ]);
    exit;
}

try {
    if ($repo->salvar($instituicaoId, $dados)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Transferência realizada com sucesso!']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao processar transferência.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno.', 'detalhe' => $e->getMessage()]);
}
