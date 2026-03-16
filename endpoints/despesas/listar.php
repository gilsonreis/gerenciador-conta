<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';

AuthHelper::requireLogin();

$mesAno = $_GET['mes'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $mesAno)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Formato de mês inválido. Use YYYY-MM']);
    exit;
}

$repo = new LancamentoRepository();
$despesas = $repo->listarPorMes(AuthHelper::getInstituicaoId(), $mesAno);
$resumo = $repo->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);

echo json_encode([
    'sucesso' => true,
    'dados' => $despesas,
    'resumo' => [
        'total_saidas' => (float)($resumo['total_saidas'] ?? 0),
        'total_saidas_formatado' => 'R$ ' . number_format((float)($resumo['total_saidas'] ?? 0), 2, ',', '.'),
        'custo_vida' => (float)($resumo['custo_vida'] ?? 0),
        'custo_vida_formatado' => 'R$ ' . number_format((float)($resumo['custo_vida'] ?? 0), 2, ',', '.')
    ]
]);
