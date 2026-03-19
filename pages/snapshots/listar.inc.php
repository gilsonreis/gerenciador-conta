<!-- Header -->
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-4">
        <a href="?pagina=home-index" class="p-2 text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors" title="Voltar ao Dashboard">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Saldos de Abertura</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Histórico de todos os snapshots mensais registrados no sistema.</p>
        </div>
    </div>

    <!-- Filtro de Ano -->
    <div class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap">Filtrar por ano:</label>
        <select id="filtro-ano" onchange="snapshotsJS.filtrar()"
            class="px-3 py-2 bg-white dark:bg-darkcard border border-gray-200 dark:border-darkborder rounded-lg text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
            <option value="">Todos</option>
            <!-- Preenchido via JS -->
        </select>
        <div id="snapshots-count" class="text-sm text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-white/5 px-3 py-2 rounded-lg whitespace-nowrap">
            — registros
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder">
                    <th class="p-4 text-sm font-semibold text-gray-600 dark:text-gray-300">
                        <i class="fa-solid fa-building-columns mr-1 text-xs opacity-60"></i> Conta
                    </th>
                    <th class="p-4 text-sm font-semibold text-gray-600 dark:text-gray-300">
                        <i class="fa-solid fa-calendar mr-1 text-xs opacity-60"></i> Mês / Ano
                    </th>
                    <th class="p-4 text-sm font-semibold text-gray-600 dark:text-gray-300 text-right">
                        <i class="fa-solid fa-vault mr-1 text-xs opacity-60"></i> Saldo de Abertura
                    </th>
                    <th class="p-4 text-sm font-semibold text-gray-600 dark:text-gray-300 text-center hidden sm:table-cell">
                        <i class="fa-solid fa-clock mr-1 text-xs opacity-60"></i> Registrado em
                    </th>
                </tr>
            </thead>
            <tbody id="tabela-snapshots" class="divide-y divide-gray-100 dark:divide-darkborder">
                <tr>
                    <td colspan="4" class="py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center">
                                <i class="fa-solid fa-spinner fa-spin text-gray-400"></i>
                            </div>
                            <span class="text-sm text-gray-400">Carregando...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
