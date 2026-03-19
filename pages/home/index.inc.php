<?php if ($role === 'super_admin'): ?>
<?php require_once __DIR__ . '/_super_admin_dashboard.inc.php'; ?>
<?php else: ?>
<div>
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Visão Geral</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-8">Acompanhe a saúde financeira da sua família no mês atual.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Card 1: Composição do Capital (Conta Armada) -->
    <div id="dash-capital-card" class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-emerald-100 dark:border-emerald-900/30">
        <div class="flex flex-col items-end space-y-1 font-mono text-sm">

            <!-- Linha 1: Saldo de Abertura (com tooltip por conta) -->
            <div class="flex justify-between w-full text-gray-500 dark:text-gray-400">
                <span>Saldo Anterior:</span>
                <!-- Tooltip Trigger -->
                <div class="relative group flex cursor-help">
                    <span class="mr-2">R$</span>
                    <span id="dash-saldo-anterior" class="w-[110px] text-right valor-sensivel border-b border-dashed border-gray-400 dark:border-gray-600">0,00</span>
                    <!-- Tooltip Bubble -->
                    <div id="tooltip-abertura" class="absolute right-0 top-6 z-50 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-xl shadow-2xl p-3 pointer-events-none">
                        <p class="font-bold text-gray-300 uppercase tracking-wider mb-2 text-[10px]">💰 Composição em 01 do mês:</p>
                        <div id="tooltip-abertura-list" class="space-y-1"></div>
                    </div>
                </div>
            </div>

            <!-- Linha 2: Entradas do Mês (com tooltip top 5) -->
            <div class="flex justify-between w-full border-b border-gray-300 dark:border-darkborder pb-1 text-gray-500 dark:text-gray-400">
                <span>+ Entradas do Mês:</span>
                <!-- Tooltip Trigger -->
                <div class="relative group flex cursor-help">
                    <span class="mr-2">R$</span>
                    <span id="dash-entradas-mes-card" class="w-[110px] text-right valor-sensivel border-b border-dashed border-gray-400 dark:border-gray-600">0,00</span>
                    <!-- Tooltip Bubble -->
                    <div id="tooltip-entradas" class="absolute right-0 top-6 z-50 hidden group-hover:block w-64 bg-gray-900 text-white text-xs rounded-xl shadow-2xl p-3 pointer-events-none">
                        <p class="font-bold text-gray-300 uppercase tracking-wider mb-2 text-[10px]">📥 Maiores receitas do mês:</p>
                        <div id="tooltip-entradas-list" class="space-y-1"></div>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="pt-2 w-full flex items-center justify-between">
                <div id="dash-capital-icon" class="p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 rounded-xl">
                    <i class="fa-solid fa-vault text-xl"></i>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-[10px] text-gray-400 dark:text-gray-500 block uppercase font-bold tracking-wider mb-1">Total Disponível Agora</span>
                    <div id="dash-capital-value-container" class="flex items-baseline text-emerald-600 dark:text-emerald-500 font-bold">
                        <span class="text-lg mr-2">R$</span>
                        <span class="text-3xl valor-sensivel w-[180px] text-right" id="dash-total-agora">0,00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Saídas do Mês -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-red-100 dark:border-red-900/30">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-red-800 dark:text-red-400/80 uppercase tracking-wider mb-1">Saídas do Mês</p>
                <h3 class="text-3xl font-bold text-red-600 dark:text-red-500 valor-sensivel" id="dash-saidas-mes">R$ 0,00</h3>
            </div>
            <div class="p-3 bg-red-50 dark:bg-red-900/20 text-red-500 rounded-xl">
                <i class="fa-solid fa-arrow-trend-down text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Card 3: Projeção de Sobra -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder relative overflow-hidden" id="dash-projecao-card">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-gray-50 dark:bg-gray-900/10 rounded-full blur-3xl z-0 pointer-events-none transition-colors duration-500" id="dash-projecao-bg"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-400 uppercase tracking-wider mb-1" id="dash-projecao-label">Fim do Mês / Sobra</p>
                <h3 class="text-3xl font-bold text-gray-600 dark:text-gray-500 transition-colors duration-500 valor-sensivel" id="dash-projecao-sobra">R$ 0,00</h3>
            </div>
            <div class="p-3 bg-gray-50 dark:bg-gray-900/20 text-gray-500 rounded-xl transition-colors duration-500" id="dash-projecao-icon">
                <i class="fa-solid fa-piggy-bank text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Custos de Vida Chart Area -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder flex flex-col items-center justify-center text-center min-h-[300px]">
        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-[#121212] flex items-center justify-center text-gray-500 mb-4 shadow-inner">
            <i class="fa-solid fa-shield-cat text-2xl"></i>
        </div>
        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Seu Custo de Vida Essencial</h4>
        <p class="text-gray-500 dark:text-gray-400 max-w-sm mb-4">Rastreie o quanto da sua renda está comprometida apenas pelo custo base de sobrevivência (Soma das despesas com flag de conta fixa).</p>
        <div class="text-3xl font-black text-gray-800 dark:text-gray-200 valor-sensivel" id="dash-custovida">R$ 0,00</div>
    </div>

    <!-- Minhas Contas Table Area -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white">Saldos Reais</h4>
            <a href="?pagina=contas-listar" class="text-sm font-medium text-primary hover:text-blue-700 dark:hover:text-blue-400">Ver todas</a>
        </div>
        <div class="overflow-y-auto flex-1 pr-2 mt-auto mb-auto" style="max-height: 250px;">
            <table class="w-full text-left border-collapse">
                <tbody id="contas-table" class="divide-y divide-gray-100 dark:divide-darkborder">
                    <!-- Javascript Data -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Próximos Vencimentos -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Próximos Vencimentos</h4>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-full">30 dias</span>
        </div>
        <div class="overflow-y-auto flex-1 pr-1 scrollbar-thin" style="max-height: 320px; scrollbar-width: thin; scrollbar-color: #d1d5db transparent;">
            <table class="w-full text-left border-collapse">
                <tbody id="vencimentos-table" class="divide-y divide-gray-100 dark:divide-darkborder">
                    <!-- Preenchido via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart Area -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Histórico -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Histórico (Últimos 12 meses)</h4>
        <div class="relative w-full h-72 flex items-center justify-center valor-sensivel">
            <canvas id="chart-historico"></canvas>
        </div>
    </div>
    <!-- Projeção -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Projeção (Próximos 12 meses)</h4>
        <div class="relative w-full h-72 flex items-center justify-center valor-sensivel">
            <canvas id="chart-projecao"></canvas>
        </div>
    </div>
</div>
<?php endif; ?>
