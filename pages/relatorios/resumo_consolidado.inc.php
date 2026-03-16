<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/RelatorioRepository.php';

AuthHelper::requireLogin();

$instId = AuthHelper::getInstituicaoId();
$repoRel = new RelatorioRepository();

// Filtros
$dataInicio = $_GET['data_inicio'] ?? date('Y-01-01');
$dataFim = $_GET['data_fim'] ?? date('Y-12-31');
$agrupamento = $_GET['agrupamento'] ?? 'mensal';
$acao = $_GET['acao'] ?? 'filtrar';

$filtros = [
    'data_inicio' => $dataInicio,
    'data_fim' => $dataFim,
    'agrupamento' => $agrupamento
];

$resultados = $repoRel->resumoConsolidado($instId, $filtros);

// Lógica de Exportação CSV
if ($acao === 'exportar_csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=resumo_consolidado_' . date('Ymd') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Periodo', 'Receitas', 'Despesas', 'Saldo']);
    
    foreach ($resultados as $r) {
        fputcsv($output, [
            $r['periodo'],
            number_format($r['total_entradas'], 2, ',', '.'),
            number_format($r['total_saidas'], 2, ',', '.'),
            number_format($r['saldo_periodo'], 2, ',', '.')
        ]);
    }
    
    fclose($output);
    exit;
}

// Preparação para Totais e Gráfico
$labels = [];
$entradas = [];
$saidas = [];
$grandTotalEntradas = 0;
$grandTotalSaidas = 0;

foreach ($resultados as $r) {
    $labels[] = $r['periodo'];
    $entradas[] = (float)$r['total_entradas'];
    $saidas[] = (float)$r['total_saidas'];
    $grandTotalEntradas += (float)$r['total_entradas'];
    $grandTotalSaidas += (float)$r['total_saidas'];
}
$grandTotalSaldo = $grandTotalEntradas - $grandTotalSaidas;
?>

<div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="?pagina=relatorios-index" class="p-2 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg transition-colors text-gray-500" title="Voltar ao Hub">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                Resumo Consolidado
            </h2>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Visão macro de faturamento e despesas agrupadas por período.</p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-darkcard rounded-xl p-6 shadow-sm border border-gray-100 dark:border-darkborder mb-6">
    <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <input type="hidden" name="pagina" value="relatorios-resumo_consolidado">
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Data Início</label>
            <input type="date" name="data_inicio" value="<?= $dataInicio ?>" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm text-gray-900 dark:text-gray-100">
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Data Fim</label>
            <input type="date" name="data_fim" value="<?= $dataFim ?>" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm text-gray-900 dark:text-gray-100">
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Agrupamento</label>
            <select name="agrupamento" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm">
                <option value="mensal" <?= $agrupamento === 'mensal' ? 'selected' : '' ?>>Mensal</option>
                <option value="anual" <?= $agrupamento === 'anual' ? 'selected' : '' ?>>Anual</option>
            </select>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" name="acao" value="filtrar" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
            <button type="submit" name="acao" value="exportar_csv" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-file-csv"></i> CSV
            </button>
        </div>
    </form>
</div>

<div class="space-y-6">
    <!-- Visualização Gráfica -->
    <div class="bg-white dark:bg-darkcard rounded-xl p-6 shadow-sm border border-gray-100 dark:border-darkborder">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chart-bar text-primary"></i> Comparativo de Fluxo
        </h3>
        <?php if (empty($resultados)): ?>
            <div class="text-center py-12">
                <p class="text-gray-500">Aperte em Filtrar para visualizar o gráfico.</p>
            </div>
        <?php else: ?>
            <div class="h-[300px] w-full">
                <canvas id="graficoConsolidado"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabela de Resultados -->
    <div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Período</th>
                        <th class="p-4 font-semibold text-right">Receitas</th>
                        <th class="p-4 font-semibold text-right">Despesas</th>
                        <th class="p-4 font-semibold text-right">Saldo do Período</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-darkborder">
                    <?php if (empty($resultados)): ?>
                        <tr><td colspan="4" class="p-8 text-center text-gray-500">Nenhum dado encontrado para o período.</td></tr>
                    <?php else: ?>
                        <?php foreach ($resultados as $r): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="p-4 font-bold text-gray-900 dark:text-gray-100"><?= $r['periodo'] ?></td>
                                <td class="p-4 text-right text-emerald-600 dark:text-emerald-400 font-medium valor-sensivel">+ R$ <?= number_format($r['total_entradas'], 2, ',', '.') ?></td>
                                <td class="p-4 text-right text-red-600 dark:text-red-400 font-medium valor-sensivel">- R$ <?= number_format($r['total_saidas'], 2, ',', '.') ?></td>
                                <td class="p-4 text-right font-bold valor-sensivel <?= $r['saldo_periodo'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' ?>">
                                    R$ <?= number_format($r['saldo_periodo'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($resultados)): ?>
                <tfoot class="bg-gray-50 dark:bg-white/5 font-bold border-t-2 border-gray-200 dark:border-darkborder">
                    <tr>
                        <td class="p-4 text-gray-800 dark:text-gray-200 uppercase text-xs">Total Consolidado</td>
                        <td class="p-4 text-right text-emerald-600 valor-sensivel">R$ <?= number_format($grandTotalEntradas, 2, ',', '.') ?></td>
                        <td class="p-4 text-right text-red-600 valor-sensivel">R$ <?= number_format($grandTotalSaidas, 2, ',', '.') ?></td>
                        <td class="p-4 text-right valor-sensivel text-lg <?= $grandTotalSaldo >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600' ?>">
                            R$ <?= number_format($grandTotalSaldo, 2, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($resultados)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('graficoConsolidado').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [
                    {
                        label: 'Receitas',
                        data: <?= json_encode($entradas) ?>,
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    },
                    {
                        label: 'Despesas',
                        data: <?= json_encode($saidas) ?>,
                        backgroundColor: '#ef4444',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563',
                            callback: value => 'R$ ' + value.toLocaleString('pt-BR')
                        },
                        grid: { color: document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    });
</script>
<?php endif; ?>
