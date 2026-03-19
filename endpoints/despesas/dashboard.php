<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();

$mesAno = $_GET['mes'] ?? date('Y-m');

// 1. Saldo Real hoje (Total em todas as contas)
$repoConta = new ContaRepository();
$saldosContas = $repoConta->saldos(AuthHelper::getInstituicaoId());
$totalReal = 0;
foreach ($saldosContas as $c) {
    $totalReal += (float)$c['saldo_atual_real'];
}

// 2. Entradas do Mês (Já recebidas)
$repoCaixa = new CaixaRepository();
$entradasMes = (float)$repoCaixa->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);

// 3. Composição Matemática (Para evitar duplicidade no visual "fator A + fator B = Total")
// Saldo Base (Anterior) + Entradas do Mês = Total Real
$saldoBase = $totalReal - $entradasMes;

// 4. Saídas do Mês (Total: Pagas + Pendentes)
$repoLancamento = new LancamentoRepository();
$resumoDespesas = $repoLancamento->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);
$saidasMes = (float)($resumoDespesas['total_saidas'] ?? 0);
$custoVida = (float)($resumoDespesas['custo_vida'] ?? 0);

// 5. Projeção de Sobra (Total Real - Saídas Totais)
$projecaoSobra = $totalReal - $saidasMes;

echo json_encode([
    'sucesso' => true,
    'saldo_base' => $saldoBase,
    'saldo_base_formatado' => 'R$ ' . number_format($saldoBase, 2, ',', '.'),
    'entradas_mes' => $entradasMes,
    'entradas_mes_formatado' => 'R$ ' . number_format($entradasMes, 2, ',', '.'),
    'total_disponivel' => $totalReal,
    'total_disponivel_formatado' => 'R$ ' . number_format($totalReal, 2, ',', '.'),
    'saidas_mes' => $saidasMes,
    'saidas_mes_formatado' => 'R$ ' . number_format($saidasMes, 2, ',', '.'),
    'projecao_sobra' => $projecaoSobra,
    'projecao_sobra_formatado' => 'R$ ' . number_format($projecaoSobra, 2, ',', '.'),
    'custovida_formatado' => 'R$ ' . number_format($custoVida, 2, ',', '.')
]);
