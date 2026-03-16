<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <div class="bg-secondary/10 p-2 rounded-lg"><i class="fa-solid fa-arrow-trend-up text-secondary"></i></div> Entradas (Caixa)
        </h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gerencie as receitas da família para o mês selecionado.</p>
    </div>
    
    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
        <!-- Navegador de Meses -->
        <div class="flex items-center bg-white dark:bg-darkcard rounded-lg border border-gray-200 dark:border-darkborder shadow-sm overflow-hidden w-full sm:w-auto justify-between">
            <button onclick="caixaJS.mudarMes(-1)" class="px-4 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors"><i class="fa-solid fa-chevron-left"></i></button>
            <span id="mes-atual-label" class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300 min-w-[150px] text-center text-sm capitalize">Carregando...</span>
            <button onclick="caixaJS.mudarMes(1)" class="px-4 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors"><i class="fa-solid fa-chevron-right"></i></button>
        </div>

        <button onclick="caixaJS.abrirModalCadastro()" class="w-full sm:w-auto bg-secondary hover:bg-emerald-600 text-white px-5 py-2 rounded-lg font-medium transition-colors shadow-sm flex justify-center items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Nova Entrada
        </button>
    </div>
</div>

<!-- Resumo Cards -->
<div class="grid grid-cols-1 mb-6">
    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/10 rounded-2xl p-6 shadow-sm border border-emerald-200 dark:border-emerald-800/30 flex justify-between items-center group transition duration-300 relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-200/50 dark:bg-emerald-500/10 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10">
            <p class="text-sm font-semibold text-emerald-800/70 dark:text-emerald-400/80 uppercase tracking-wider mb-2">Total de Entradas no Mês</p>
            <h3 class="text-4xl font-black text-emerald-700 dark:text-emerald-400 tracking-tight" id="resumo-total-entradas">R$ 0,00</h3>
        </div>
        <div class="hidden sm:flex items-center justify-center w-16 h-16 rounded-full bg-emerald-200/50 dark:bg-emerald-800/50 text-emerald-600 dark:text-emerald-300 relative z-10">
            <i class="fa-solid fa-piggy-bank text-3xl"></i>
        </div>
    </div>
</div>

<!-- Table / List -->
<div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="overflow-x-auto" style="min-height: 200px;">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-semibold">Data</th>
                    <th class="p-4 font-semibold">Origem</th>
                    <th class="p-4 font-semibold text-right">Valor</th>
                    <th class="p-4 font-semibold w-24 text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-caixa" class="divide-y divide-gray-100 dark:divide-darkborder">
                <!-- Preenchido via AJAX -->
                <tr><td colspan="4" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/_form.inc.php'; ?>
