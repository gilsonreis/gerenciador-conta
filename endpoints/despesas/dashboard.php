<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';

AuthHelper::requireLogin();

$mesAno = $_GET['mes'] ?? date('Y-m');

$repoLancamento = new LancamentoRepository();
$resumoDespesas = $repoLancamento->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);
$saidas = (float)($resumoDespesas['total_saidas'] ?? 0);
$custoVida = (float)($resumoDespesas['custo_vida'] ?? 0);

$repoCaixa = new CaixaRepository();
$entradas = (float)$repoCaixa->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);

$saldo = $entradas - $saidas;

echo json_encode([
    'sucesso' => true,
    'entradas' => $entradas,
    'entradas_formatado' => 'R$ ' . number_format($entradas, 2, ',', '.'),
    'saidas' => $saidas,
    'saidas_formatado' => 'R$ ' . number_format($saidas, 2, ',', '.'),
    'custovida' => $custoVida,
    'custovida_formatado' => 'R$ ' . number_format($custoVida, 2, ',', '.'),
    'saldo' => $saldo,
    'saldo_formatado' => 'R$ ' . number_format($saldo, 2, ',', '.')
]);
