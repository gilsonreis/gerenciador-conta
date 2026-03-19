<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Services/BalanceService.php';

AuthHelper::requireLogin();

$mesAno = $_GET['mes'] ?? date('Y-m');

$service = new BalanceService();
$saldos  = $service->getSaldosInstituicao(AuthHelper::getInstituicaoId(), $mesAno);

echo json_encode(['sucesso' => true, 'dados' => $saldos]);
