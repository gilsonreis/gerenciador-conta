<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();

$repo = new ContaRepository();
$saldos = $repo->saldos(AuthHelper::getInstituicaoId());

// Formatando para BRL
foreach ($saldos as &$conta) {
    $conta['saldo_atual_formatado'] = 'R$ ' . number_format((float)$conta['saldo_atual_real'], 2, ',', '.');
}

echo json_encode(['sucesso' => true, 'dados' => $saldos]);
