<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/RelatorioRepository.php';

AuthHelper::requireLogin();

$instId = AuthHelper::getInstituicaoId();
$repoRel = new RelatorioRepository();

// Filtros
$mes = (int)($_GET['mes'] ?? date('m'));
$ano = (int)($_GET['ano'] ?? date('Y'));
$statusVencimento = $_GET['status_vencimento'] ?? 'todos';

$filtros = [
    'mes' => $mes,
    'ano' => $ano,
    'status_vencimento' => $statusVencimento
];

$resultados = $repoRel->contasAPagar($instId, $filtros);

$totalPendente = 0;
foreach ($resultados as $r) {
    $totalPendente += (float)$r['valor'];
}

$mesesNomes = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

$hoje = date('Y-m-d');
?>

<div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="?pagina=relatorios-index" class="p-2 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg transition-colors text-gray-500" title="Voltar ao Hub">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                Contas a Pagar (Pendências)
            </h2>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Visualize suas contas em aberto e identifique atrasos.</p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-darkcard rounded-xl p-6 shadow-sm border border-gray-100 dark:border-darkborder mb-6">
    <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <input type="hidden" name="pagina" value="relatorios-contas_pagar">
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Mês Ref.</label>
            <select name="mes" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm">
                <?php foreach ($mesesNomes as $num => $nome): ?>
                    <option value="<?= $num ?>" <?= $mes == $num ? 'selected' : '' ?>><?= $nome ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ano Ref.</label>
            <select name="ano" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm">
                <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $ano == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status de Vencimento</label>
            <select name="status_vencimento" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm">
                <option value="todos" <?= $statusVencimento === 'todos' ? 'selected' : '' ?>>Todos em Aberto</option>
                <option value="atrasados" <?= $statusVencimento === 'atrasados' ? 'selected' : '' ?>>Atrasados</option>
                <option value="mes" <?= $statusVencimento === 'mes' ? 'selected' : '' ?>>Vencem neste mês</option>
                <option value="futuro" <?= $statusVencimento === 'futuro' ? 'selected' : '' ?>>Vencem no futuro</option>
            </select>
        </div>
        
        <div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Tabela de Pendências -->
<div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-semibold">Vencimento</th>
                    <th class="p-4 font-semibold">Descrição</th>
                    <th class="p-4 font-semibold">Parcela</th>
                    <th class="p-4 font-semibold">Responsável</th>
                    <th class="p-4 font-semibold">Situação</th>
                    <th class="p-4 font-semibold text-right">Valor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-darkborder">
                <?php if (empty($resultados)): ?>
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">Nenhuma conta pendente encontrada para este filtro.</td></tr>
                <?php else: ?>
                    <?php foreach ($resultados as $r): 
                        $venc = $r['data_vencimento'];
                        $badgeClass = "bg-gray-100 text-gray-600";
                        $badgeText = "A Vencer";

                        if ($venc < $hoje) {
                            $badgeClass = "bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400";
                            $badgeText = "Atrasado";
                        } elseif ($venc === $hoje) {
                            $badgeClass = "bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400";
                            $badgeText = "Vence Hoje";
                        } else {
                            $badgeClass = "bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400";
                        }
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4 text-sm font-medium <?= $venc < $hoje ? 'text-red-500' : '' ?>">
                                <?= date('d/m/Y', strtotime($venc)) ?>
                            </td>
                            <td class="p-4 font-medium text-gray-900 dark:text-gray-100">
                                <?= htmlspecialchars($r['descricao']) ?>
                                <p class="text-[10px] text-gray-400 uppercase"><?= htmlspecialchars($r['categoria_nome']) ?></p>
                            </td>
                            <td class="p-4 text-sm text-gray-500"><?= $r['numero_parcela'] ?>/<?= $r['total_parcelas'] ?></td>
                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($r['usuario_nome']) ?></td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase <?= $badgeClass ?>">
                                    <?= $badgeText ?>
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-gray-900 dark:text-gray-100 valor-sensivel">
                                R$ <?= number_format($r['valor'], 2, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($resultados)): ?>
            <tfoot class="bg-gray-50 dark:bg-white/5 font-bold">
                <tr>
                    <td colspan="5" class="p-4 text-right text-gray-800 dark:text-gray-200 uppercase text-xs">Total Pendente no Filtro:</td>
                    <td class="p-4 text-right text-red-600 valor-sensivel text-lg">R$ <?= number_format($totalPendente, 2, ',', '.') ?></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
