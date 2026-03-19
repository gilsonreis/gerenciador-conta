<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();
$instituicaoId = AuthHelper::getInstituicaoId();
$mesAno = $_GET['mes'] ?? date('Y-m');

// 1. Saldo Real hoje (Sum of all accounts - matches "Saldos Reais" box)
$repoConta = new ContaRepository();
$saldosContas = $repoConta->saldos($instituicaoId);
$totalNasContas = 0;
foreach ($saldosContas as $c) {
    $totalNasContas += (float)$c['saldo_atual_real'];
}

// 2. Entradas do Mês (Total registered in the month)
$repoCaixa = new CaixaRepository();
$entradasMes = (float)$repoCaixa->resumoMes($instituicaoId, $mesAno);

// 3. Mathematical Sum (Per user's explicit request: Bank Total + Monthly Income = Total Available)
$totalDisponivel = $totalNasContas + $entradasMes;

// 4. Saídas do Mês (Total: Paid + Pending)
$repoLancamento = new LancamentoRepository();
$resumoDespesas = $repoLancamento->resumoMes($instituicaoId, $mesAno);
$saidasMes = (float)($resumoDespesas['total_saidas'] ?? 0);
$custoVida = (float)($resumoDespesas['custo_vida'] ?? 0);

// 5. Projeção de Sobra (Unified Total Available - Total Expenses)
$projecaoSobra = $totalDisponivel - $saidasMes;

echo json_encode([
    'sucesso' => true,
    'saldo_base' => $totalNasContas,
    'saldo_base_formatado' => 'R$ ' . number_format($totalNasContas, 2, ',', '.'),
    'entradas_mes' => $entradasMes,
    'entradas_mes_formatado' => 'R$ ' . number_format($entradasMes, 2, ',', '.'),
    'total_disponivel' => $totalDisponivel,
    'total_disponivel_formatado' => 'R$ ' . number_format($totalDisponivel, 2, ',', '.'),
    'saidas_mes' => $saidasMes,
    'saidas_mes_formatado' => 'R$ ' . number_format($saidasMes, 2, ',', '.'),
    'projecao_sobra' => $projecaoSobra,
    'projecao_sobra_formatado' => 'R$ ' . number_format($projecaoSobra, 2, ',', '.'),
    'custovida_formatado' => 'R$ ' . number_format($custoVida, 2, ',', '.')
]);
