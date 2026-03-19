<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';
require_once __DIR__ . '/../../src/Repositories/SnapshotRepository.php';

AuthHelper::requireLogin();
$instituicaoId = AuthHelper::getInstituicaoId();
$mesAno = $_GET['mes'] ?? date('Y-m');

// 1. Saldo de Abertura (Marco Zero Mensal)
// Busca a soma dos snapshots salvos no dia 01 do mês
$repoSnapshot = new SnapshotRepository();
$saldoAbertura = $repoSnapshot->somaAberturaMes($instituicaoId, $mesAno);

// Fallback: se não houver snapshot, estimamos como (saldo real atual - entradas do mês)
if ($saldoAbertura === null) {
    $repoConta = new ContaRepository();
    $saldosContas = $repoConta->saldos($instituicaoId);
    $totalNasContas = 0;
    foreach ($saldosContas as $c) {
        $totalNasContas += (float)$c['saldo_atual_real'];
    }

    $repoCaixaFallback = new CaixaRepository();
    $entradasFallback = (float)$repoCaixaFallback->resumoMes($instituicaoId, $mesAno);
    $saldoAbertura = $totalNasContas - $entradasFallback;
}

// 2. Entradas do Mês (Total registradas no mês)
$repoCaixa = new CaixaRepository();
$entradasMes = (float)$repoCaixa->resumoMes($instituicaoId, $mesAno);

// 3. Total Disponível = Saldo de Abertura + Entradas do Mês
$totalDisponivel = $saldoAbertura + $entradasMes;

// 4. Saídas do Mês (Total: Pagas + Pendentes)
$repoLancamento = new LancamentoRepository();
$resumoDespesas = $repoLancamento->resumoMes($instituicaoId, $mesAno);
$saidasMes = (float)($resumoDespesas['total_saidas'] ?? 0);
$custoVida = (float)($resumoDespesas['custo_vida'] ?? 0);

// 5. Projeção de Sobra = Total Disponível - Saídas do Mês
$projecaoSobra = $totalDisponivel - $saidasMes;

// 6. Detalhamento para Tooltips
// a) Detalhes do snapshot de abertura (por conta)
$detalhesAbertura = $repoSnapshot->listarPorMes($instituicaoId, $mesAno);
$tooltipAbertura = array_map(fn($row) => [
    'conta' => $row['conta_nome'],
    'valor' => 'R$ ' . number_format((float)$row['valor_abertura'], 2, ',', '.')
], $detalhesAbertura);

// Fallback textual se não houver snapshots
if (empty($tooltipAbertura)) {
    $tooltipAbertura = [['conta' => 'Estimativa', 'valor' => 'Nenhum snapshot encontrado para este mês.']];
}

// b) Top 5 entradas do mês (por valor DESC)
$detalhesEntradas = $repoCaixa->listarPorMes($instituicaoId, $mesAno);
usort($detalhesEntradas, fn($a, $b) => $b['valor'] <=> $a['valor']);
$top5Entradas = array_slice($detalhesEntradas, 0, 5);
$tooltipEntradas = array_map(fn($row) => [
    'descricao' => $row['descricao'] ?: 'Sem descrição',
    'valor' => 'R$ ' . number_format((float)$row['valor'], 2, ',', '.')
], $top5Entradas);

if (empty($tooltipEntradas)) {
    $tooltipEntradas = [['descricao' => 'Nenhuma entrada registrada ainda.', 'valor' => '']];
}

echo json_encode([
    'sucesso' => true,
    'saldo_base' => $saldoAbertura,
    'saldo_base_formatado' => 'R$ ' . number_format($saldoAbertura, 2, ',', '.'),
    'entradas_mes' => $entradasMes,
    'entradas_mes_formatado' => 'R$ ' . number_format($entradasMes, 2, ',', '.'),
    'total_disponivel' => $totalDisponivel,
    'total_disponivel_formatado' => 'R$ ' . number_format($totalDisponivel, 2, ',', '.'),
    'saidas_mes' => $saidasMes,
    'saidas_mes_formatado' => 'R$ ' . number_format($saidasMes, 2, ',', '.'),
    'projecao_sobra' => $projecaoSobra,
    'projecao_sobra_formatado' => 'R$ ' . number_format($projecaoSobra, 2, ',', '.'),
    'custovida_formatado' => 'R$ ' . number_format($custoVida, 2, ',', '.'),
    'tooltip_abertura' => $tooltipAbertura,
    'tooltip_entradas' => $tooltipEntradas,
]);

