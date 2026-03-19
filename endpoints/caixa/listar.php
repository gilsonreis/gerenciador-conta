<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';

AuthHelper::requireLogin();

$mesAno = $_GET['mes'] ?? date('Y-m'); // Expected YYYY-MM
if (!preg_match('/^\d{4}-\d{2}$/', $mesAno)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Formato de mês inválido. Use YYYY-MM']);
    exit;
}

$repo = new CaixaRepository();
$entradas = $repo->listarPorMes(AuthHelper::getInstituicaoId(), $mesAno);
$resumo = $repo->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);

$total = (float)$resumo['total_recebido'] + (float)$resumo['total_pendente'];

echo json_encode([
    'sucesso' => true, 
    'dados' => $entradas, 
    'resumo' => [
        'total_confirmado' => $resumo['total_recebido'],
        'total_pendente' => $resumo['total_pendente'],
        'total_entradas' => $total,
        'total_entradas_formatado' => 'R$ ' . number_format($total, 2, ',', '.')
    ]
]);
