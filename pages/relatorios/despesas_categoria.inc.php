<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/RelatorioRepository.php';

AuthHelper::requireLogin();

$instId = AuthHelper::getInstituicaoId();
$repoRel = new RelatorioRepository();

// Filtros
$mes = (int)($_GET['mes'] ?? date('m'));
$ano = (int)($_GET['ano'] ?? date('Y'));
$status = $_GET['status'] ?? 'todas';

$resultados = $repoRel->despesasPorCategoria($instId, $mes, $ano, $status);

// Cálculo do Total Geral e Preparação para o Gráfico
$totalGeral = 0;
$labels = [];
$data = [];
$cores = [
    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
    '#ec4899', '#06b6d4', '#f97316', '#14b8a6', '#6366f1'
];

foreach ($resultados as $r) {
    $totalGeral += (float)$r['total'];
    $labels[] = $r['categoria_nome'];
    $data[] = (float)$r['total'];
}

$mesesNomes = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
?>

<div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="?pagina=relatorios-index" class="p-2 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg transition-colors text-gray-500" title="Voltar ao Hub">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                Despesas por Categoria
            </h2>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Analise a distribuição dos seus gastos mensais.</p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-darkcard rounded-xl p-6 shadow-sm border border-gray-100 dark:border-darkborder mb-6">
    <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <input type="hidden" name="pagina" value="relatorios-despesas_categoria">
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Mês</label>
            <select name="mes" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm">
                <?php foreach ($mesesNomes as $num => $nome): ?>
                    <option value="<?= $num ?>" <?= $mes == $num ? 'selected' : '' ?>><?= $nome ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ano</label>
            <select name="ano" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm">
                <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $ano == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm">
                <option value="todas" <?= $status === 'todas' ? 'selected' : '' ?>>Todas as Parcelas</option>
                <option value="pagas" <?= $status === 'pagas' ? 'selected' : '' ?>>Apenas Pagas</option>
                <option value="pendentes" <?= $status === 'pendentes' ? 'selected' : '' ?>>Apenas Pendentes</option>
            </select>
        </div>
        
        <div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Visualização Gráfica -->
    <div class="bg-white dark:bg-darkcard rounded-xl p-6 shadow-sm border border-gray-100 dark:border-darkborder flex flex-col items-center justify-center">
        <?php if (empty($resultados)): ?>
            <div class="text-center py-12">
                <i class="fa-solid fa-chart-line text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Nenhum dado para exibir no gráfico.</p>
            </div>
        <?php else: ?>
            <div class="w-full max-w-[400px]">
                <canvas id="graficoCategorias"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabela de Dados -->
    <div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-darkborder bg-gray-50/50 dark:bg-white/5">
            <h3 class="font-bold text-gray-800 dark:text-white">Detalhamento por Categoria</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Categoria</th>
                        <th class="p-4 font-semibold text-right">Valor Total</th>
                        <th class="p-4 font-semibold text-right w-24">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-darkborder">
                    <?php if (empty($resultados)): ?>
                        <tr><td colspan="3" class="p-8 text-center text-gray-500">Nenhum registro encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($resultados as $index => $r): 
                            $percent = ($totalGeral > 0) ? ($r['total'] / $totalGeral) * 100 : 0;
                            $corBase = $cores[$index % count($cores)];
                        ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="p-4 flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full" style="background-color: <?= $corBase ?>"></div>
                                    <span class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars($r['categoria_nome']) ?></span>
                                </td>
                                <td class="p-4 text-right font-bold text-red-500 valor-sensivel">
                                    R$ <?= number_format($r['total'], 2, ',', '.') ?>
                                </td>
                                <td class="p-4 text-right text-sm text-gray-500">
                                    <?= number_format($percent, 1, ',', '.') ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($resultados)): ?>
                <tfoot class="bg-gray-50 dark:bg-white/5 font-bold">
                    <tr>
                        <td class="p-4 text-gray-800 dark:text-gray-200">Total do Período</td>
                        <td class="p-4 text-right text-red-600 valor-sensivel">R$ <?= number_format($totalGeral, 2, ',', '.') ?></td>
                        <td class="p-4 text-right text-gray-800 dark:text-gray-200">100%</td>
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
        const ctx = document.getElementById('graficoCategorias').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($data) ?>,
                    backgroundColor: <?= json_encode(array_slice($cores, 0, count($labels))) ?>,
                    borderWidth: 2,
                    borderColor: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563',
                            font: { size: 12 },
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) label += ': ';
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
<?php endif; ?>
