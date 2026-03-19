<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();

$mesAno = $_GET['mes'] ?? date('Y-m');

// 1. Saldo Atual (Dinheiro Real hoje em todas as contas)
$repoConta = new ContaRepository();
$saldosContas = $repoConta->saldos(AuthHelper::getInstituicaoId());
$saldoAtualReais = 0;
foreach ($saldosContas as $c) {
    $saldoAtualReais += (float)$c['saldo_atual_real'];
}

// 2. Saídas do Mês (Total: Pagas + Pendentes)
$repoLancamento = new LancamentoRepository();
$resumoDespesas = $repoLancamento->resumoMes(AuthHelper::getInstituicaoId(), $mesAno);
$saidasMes = (float)($resumoDespesas['total_saidas'] ?? 0);
$custoVida = (float)($resumoDespesas['custo_vida'] ?? 0);

// 3. Projeção de Sobra (Saldo Atual - Total de Saídas do Mês)
$projecaoSobra = $saldoAtualReais - $saidasMes;

echo json_encode([
    'sucesso' => true,
    'saldo_atual' => $saldoAtualReais,
    'saldo_atual_formatado' => 'R$ ' . number_format($saldoAtualReais, 2, ',', '.'),
    'saidas_mes' => $saidasMes,
    'saidas_mes_formatado' => 'R$ ' . number_format($saidasMes, 2, ',', '.'),
    'projecao_sobra' => $projecaoSobra,
    'projecao_sobra_formatado' => 'R$ ' . number_format($projecaoSobra, 2, ',', '.'),
    'custovida_formatado' => 'R$ ' . number_format($custoVida, 2, ',', '.')
]);
