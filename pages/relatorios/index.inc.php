<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';

AuthHelper::requireLogin();
?>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
        <div class="bg-blue-500/10 p-2 rounded-lg"><i class="fa-solid fa-chart-pie text-blue-500"></i></div> Central de Relatórios
    </h2>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Selecione uma análise abaixo para visualizar os dados financeiros da sua instituição.</p>
</div>

<!-- Hub Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <!-- Card: Fluxo de Caixa -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder hover:shadow-md transition-shadow group">
        <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-500 group-hover:text-white transition-all text-blue-500">
            <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
        </div>
        
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Fluxo de Caixa Detalhado</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Visualize entradas e saídas por período, com filtros de conta e tipo, e opção de exportação para CSV.</p>
        
        <a href="?pagina=relatorios-fluxo_caixa" class="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all">
            Acessar Relatório <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

    <!-- Card: Despesas por Categoria -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder hover:shadow-md transition-shadow group">
        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-all text-emerald-500">
            <i class="fa-solid fa-chart-pie text-xl"></i>
        </div>
        
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Despesas por Categoria</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Analise para onde seu dinheiro está indo. Agrupamento de gastos mensais por categoria.</p>
        
        <a href="?pagina=relatorios-despesas_categoria" class="inline-flex items-center gap-2 text-emerald-500 font-semibold hover:gap-3 transition-all">
            Acessar Relatório <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

    <!-- Card: Contas a Pagar -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder hover:shadow-md transition-shadow group">
        <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-500 group-hover:text-white transition-all text-amber-500">
            <i class="fa-solid fa-calendar-days text-xl"></i>
        </div>
        
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Contas a Pagar (Pendências)</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Visualize suas contas em aberto, filtre por mês de vencimento e identifique faturas atrasadas.</p>
        
        <a href="?pagina=relatorios-contas_pagar" class="inline-flex items-center gap-2 text-amber-500 font-semibold hover:gap-3 transition-all">
            Acessar Relatório <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

    <!-- Card: Resumo Consolidado -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder hover:shadow-md transition-shadow group">
        <div class="w-12 h-12 bg-indigo-500/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-500 group-hover:text-white transition-all text-indigo-500">
            <i class="fa-solid fa-chart-column text-xl"></i>
        </div>
        
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Resumo Consolidado</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Visão macro do seu faturamento e despesas. Agrupamento de totais por mês ou por ano para análise de crescimento.</p>
        
        <a href="?pagina=relatorios-resumo_consolidado" class="inline-flex items-center gap-2 text-indigo-500 font-semibold hover:gap-3 transition-all">
            Acessar Relatório <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

    <!-- Futuros Relatórios (Placeholders) -->
    <!-- <div class="bg-gray-50/50 dark:bg-white/5 rounded-2xl p-6 border border-dashed border-gray-200 dark:border-darkborder flex flex-col items-center justify-center text-center opacity-60">
        <div class="w-12 h-12 bg-gray-200 dark:bg-darkborder rounded-xl flex items-center justify-center mb-4 text-gray-400">
            <i class="fa-solid fa-lock text-xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-400 mb-1">Análise de Categorias</h3>
        <p class="text-gray-400 text-xs">Em breve...</p>
    </div>

    <div class="bg-gray-50/50 dark:bg-white/5 rounded-2xl p-6 border border-dashed border-gray-200 dark:border-darkborder flex flex-col items-center justify-center text-center opacity-60">
        <div class="w-12 h-12 bg-gray-200 dark:bg-darkborder rounded-xl flex items-center justify-center mb-4 text-gray-400">
            <i class="fa-solid fa-lock text-xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-400 mb-1">Projeção Anual</h3>
        <p class="text-gray-400 text-xs">Em breve...</p>
    </div> -->

</div>
