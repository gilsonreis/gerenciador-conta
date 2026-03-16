<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <div class="bg-red-500/10 p-2 rounded-lg"><i class="fa-solid fa-arrow-trend-down text-red-500"></i></div> Despesas
        </h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gerencie pagamentos e parcelas para o mês selecionado.</p>
    </div>
    
    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
        <!-- Navegador de Meses -->
        <div class="flex items-center bg-white dark:bg-darkcard rounded-lg border border-gray-200 dark:border-darkborder shadow-sm overflow-hidden w-full sm:w-auto justify-between">
            <button onclick="despesasJS.mudarMes(-1)" class="px-4 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors"><i class="fa-solid fa-chevron-left"></i></button>
            <span id="mes-atual-label" class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300 min-w-[150px] text-center text-sm capitalize">Carregando...</span>
            <button onclick="despesasJS.mudarMes(1)" class="px-4 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors"><i class="fa-solid fa-chevron-right"></i></button>
        </div>

        <button onclick="despesasJS.abrirModalCadastro()" class="w-full sm:w-auto bg-primary hover:bg-blue-600 text-white px-5 py-2 rounded-lg font-medium transition-colors shadow-sm flex justify-center items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Nova Despesa
        </button>
    </div>
</div>

<!-- Resumo Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
    <!-- Card Saidas Totais -->
    <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/10 rounded-2xl p-6 shadow-sm border border-red-200 dark:border-red-800/30 flex justify-between items-center group transition duration-300 relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-red-200/50 dark:bg-red-500/10 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10">
            <p class="text-xs font-bold text-red-800/70 dark:text-red-400/80 uppercase tracking-widest mb-2 flex items-center gap-2">
                <i class="fa-solid fa-calculator"></i> Total Previsto no Mês
            </p>
            <h3 class="text-3xl font-black text-red-700 dark:text-red-400 tracking-tight valor-sensivel" id="resumo-total-saidas">R$ 0,00</h3>
        </div>
        <div class="hidden sm:flex items-center justify-center w-14 h-14 rounded-full bg-red-200/50 dark:bg-red-800/50 text-red-600 dark:text-red-300 relative z-10 shadow-inner">
            <i class="fa-solid fa-money-bill-transfer text-2xl"></i>
        </div>
    </div>

    <!-- Card Custo de Vida -->
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/40 dark:to-gray-900/40 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-darkborder flex justify-between items-center group transition duration-300 relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-gray-200/50 dark:bg-white/5 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10">
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                <i class="fa-solid fa-shield-cat cursor-help" title="Despesas marcadas como Contas Fixas essenciais"></i> Sobrevivência (Contas Fixas)
            </p>
            <h3 class="text-3xl font-black text-gray-700 dark:text-gray-300 tracking-tight valor-sensivel" id="resumo-custo-vida">R$ 0,00</h3>
        </div>
        <div class="hidden sm:flex items-center justify-center w-14 h-14 rounded-full bg-gray-200 dark:bg-darkborder text-gray-500 dark:text-gray-400 relative z-10 shadow-inner">
            <i class="fa-solid fa-house-chimney text-xl"></i>
        </div>
    </div>
</div>

<!-- Filtros Avançados -->
<div class="flex flex-col sm:flex-row gap-4 mb-4">
    <!-- Dropdown Categoria -->
    <div class="w-full sm:w-1/3">
        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Categoria</label>
        <select id="filtro-categoria" onchange="despesasJS.carregar()" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-gray-100">
            <option value="">Todas as Categorias</option>
        </select>
    </div>
    
    <!-- Dropdown Conta Fixa -->
    <div class="w-full sm:w-1/3">
        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tipo de Despesa</label>
        <select id="filtro-conta-fixa" onchange="despesasJS.carregar()" class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-gray-100">
            <option value="">Todas</option>
            <option value="1">Contas Fixas</option>
            <option value="0">Variáveis</option>
        </select>
    </div>
</div>

<!-- Filtros Rápidos -->
<div class="flex gap-2 mb-4 overflow-x-auto pb-2 scrollbar-hide">
    <button onclick="despesasJS.filtrar('todas')" id="filtro-todas" class="px-4 py-1.5 rounded-full text-sm font-medium bg-gray-800 text-white dark:bg-white dark:text-gray-900 whitespace-nowrap transition-colors">Todas</button>
    <button onclick="despesasJS.filtrar('pendentes')" id="filtro-pendentes" class="px-4 py-1.5 rounded-full text-sm font-medium bg-gray-200 text-gray-600 dark:bg-darkborder dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-white/10 whitespace-nowrap transition-colors">Pendentes</button>
    <button onclick="despesasJS.filtrar('pagas')" id="filtro-pagas" class="px-4 py-1.5 rounded-full text-sm font-medium bg-gray-200 text-gray-600 dark:bg-darkborder dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-white/10 whitespace-nowrap transition-colors">Pagas</button>
</div>

<!-- Tabela de Lançamentos -->
<div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="overflow-x-auto" style="min-height: 300px;">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-semibold w-12 text-center">St</th>
                    <th class="p-4 font-semibold">Vencimento</th>
                    <th class="p-4 font-semibold">Descrição</th>
                    <th class="p-4 font-semibold">Categoria</th>
                    <th class="p-4 font-semibold text-right">Valor</th>
                    <th class="p-4 font-semibold w-24 text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-despesas" class="divide-y divide-gray-100 dark:divide-darkborder">
                <!-- Preenchido via AJAX -->
                <tr><td colspan="6" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando suas finanças...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php 
require_once __DIR__ . '/_form.inc.php'; 
require_once __DIR__ . '/_detalhes.inc.php'; 
?>
