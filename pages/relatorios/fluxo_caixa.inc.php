<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/RelatorioRepository.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();

$instId = AuthHelper::getInstituicaoId();
$repoRel = new RelatorioRepository();
$repoConta = new ContaRepository();

$contas = $repoConta->listar($instId);

// Inicializa filtros com valores padrão ou GET
$filtros = [
    'data_inicio' => $_GET['data_inicio'] ?? date('Y-m-01'),
    'data_fim' => $_GET['data_fim'] ?? date('Y-m-t'),
    'conta_id' => $_GET['conta_id'] ?? '',
    'tipo_movimento' => $_GET['tipo_movimento'] ?? 'todos'
];

$acao = $_GET['acao'] ?? '';
$resultados = [];

if ($acao === 'filtrar' || $acao === 'exportar_csv') {
    $resultados = $repoRel->fluxoCaixaDetalhado($instId, $filtros);
}

// Lógica de Exportação CSV
if ($acao === 'exportar_csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=relatorio_fluxo_' . date('Ymd') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Header do CSV
    fputcsv($output, ['Data', 'Descricao', 'Categoria', 'Conta', 'Tipo', 'Valor']);
    
    foreach ($resultados as $r) {
        fputcsv($output, [
            date('d/m/Y', strtotime($r['data_movimento'])),
            $r['descricao'],
            $r['categoria_nome'],
            $r['conta_nome'],
            $r['tipo'],
            number_format($r['valor'], 2, ',', '.')
        ]);
    }
    
    fclose($output);
    exit;
}

// Cálculo de Totais para a tela
$totalEntradas = 0;
$totalSaidas = 0;
foreach ($resultados as $r) {
    if ($r['tipo'] === 'Entrada') $totalEntradas += $r['valor'];
    else $totalSaidas += $r['valor'];
}
$saldoPeriodo = $totalEntradas - $totalSaidas;
?>

<div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="?pagina=relatorios-index" class="p-2 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg transition-colors text-gray-500" title="Voltar ao Hub">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                Fluxo de Caixa Detalhado
            </h2>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Visualize entradas e saídas por período, com opção de exportação para CSV.</p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-darkcard rounded-xl p-6 shadow-sm border border-gray-100 dark:border-darkborder mb-6">
    <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <input type="hidden" name="pagina" value="relatorios-fluxo_caixa">
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Data Início</label>
            <input type="date" name="data_inicio" value="<?= $filtros['data_inicio'] ?>" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-gray-100">
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Data Fim</label>
            <input type="date" name="data_fim" value="<?= $filtros['data_fim'] ?>" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-gray-100">
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Conta Bancária</label>
            <select name="conta_id" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-gray-100">
                <option value="">Todas as Contas</option>
                <?php foreach ($contas as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filtros['conta_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tipo de Movimento</label>
            <select name="tipo_movimento" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-gray-100">
                <option value="todos" <?= $filtros['tipo_movimento'] === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="entrada" <?= $filtros['tipo_movimento'] === 'entrada' ? 'selected' : '' ?>>Apenas Entradas</option>
                <option value="saida" <?= $filtros['tipo_movimento'] === 'saida' ? 'selected' : '' ?>>Apenas Saídas</option>
            </select>
        </div>
        
        <div class="md:col-span-4 flex justify-end gap-3 mt-2">
            <button type="submit" name="acao" value="filtrar" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-filter"></i> Filtrar na Tela
            </button>
            <button type="submit" name="acao" value="exportar_csv" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-csv"></i> Exportar CSV
            </button>
        </div>
    </form>
</div>

<?php if ($acao === 'filtrar'): ?>
<!-- Tabela de Resultados -->
<div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-semibold">Data</th>
                    <th class="p-4 font-semibold">Descrição</th>
                    <th class="p-4 font-semibold">Categoria</th>
                    <th class="p-4 font-semibold">Conta</th>
                    <th class="p-4 font-semibold">Tipo</th>
                    <th class="p-4 font-semibold text-right">Valor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-darkborder">
                <?php if (empty($resultados)): ?>
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">Nenhum movimento encontrado para o período selecionado.</td></tr>
                <?php else: ?>
                    <?php foreach ($resultados as $r): 
                        $corValor = $r['tipo'] === 'Entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400';
                        $iconTipo = $r['tipo'] === 'Entrada' ? 'fa-arrow-up' : 'fa-arrow-down';
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4 text-sm text-gray-500 dark:text-gray-300"><?= date('d/m/Y', strtotime($r['data_movimento'])) ?></td>
                            <td class="p-4 font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars($r['descricao']) ?></td>
                            <td class="p-4"><span class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded text-xs"><?= htmlspecialchars($r['categoria_nome']) ?></span></td>
                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($r['conta_nome']) ?></td>
                            <td class="p-4">
                                <span class="flex items-center gap-1 text-xs font-bold uppercase <?= $corValor ?>">
                                    <i class="fa-solid <?= $iconTipo ?>"></i> <?= $r['tipo'] ?>
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold <?= $corValor ?> valor-sensivel">
                                <?= ($r['tipo'] === 'Saída' ? '- ' : '+ ') ?> R$ <?= number_format($r['valor'], 2, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-white/5 font-bold">
                <tr>
                    <td colspan="5" class="p-4 text-right text-emerald-600 dark:text-emerald-400">Total Entradas:</td>
                    <td class="p-4 text-right text-emerald-600 dark:text-emerald-400 valor-sensivel">R$ <?= number_format($totalEntradas, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="p-4 text-right text-red-600 dark:text-red-400">Total Saídas:</td>
                    <td class="p-4 text-right text-red-600 dark:text-red-400 valor-sensivel">R$ <?= number_format($totalSaidas, 2, ',', '.') ?></td>
                </tr>
                <tr class="border-t border-gray-200 dark:border-darkborder text-lg">
                    <td colspan="5" class="p-4 text-right">Saldo do Período:</td>
                    <td class="p-4 text-right valor-sensivel <?= $saldoPeriodo >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' ?>">R$ <?= number_format($saldoPeriodo, 2, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endif; ?>
