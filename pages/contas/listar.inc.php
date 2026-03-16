<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Minhas Contas e Carteiras</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie seu fluxo de caixa por conta bancária</p>
    </div>
    <button onclick="contasJS.abrirModalCadastro()" class="px-4 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Nova Conta
    </button>
</div>

<div class="bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder">
                    <th class="p-4 text-sm font-semibold text-gray-600 dark:text-gray-300">Conta / Carteira</th>
                    <th class="p-4 text-sm font-semibold text-gray-600 dark:text-gray-300 text-right">Saldo Inicial</th>
                    <th class="p-4 text-sm font-semibold text-gray-600 dark:text-gray-300 w-24 text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-contas" class="divide-y divide-gray-100 dark:divide-darkborder">
                <!-- Preenchido via JS -->
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/_form.inc.php'; ?>
