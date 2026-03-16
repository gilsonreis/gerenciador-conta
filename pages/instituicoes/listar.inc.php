<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Instituições</h2>
        <p class="text-gray-500 dark:text-gray-400">Gerencie as instituições ou grupos familiares ao qual você pertence.</p>
    </div>
    <button onclick="instituicoesJS.abrirModalCadastro()" class="px-4 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Nova Instituição
    </button>
</div>

<!-- Tabela -->
<div class="bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold">
                    <th class="p-4 rounded-tl-lg">Nome da Instituição</th>
                    <th class="p-4 text-center rounded-tr-lg w-32">Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-instituicoes" class="divide-y divide-gray-100 dark:divide-darkborder">
                <tr><td colspan="2" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/_form.inc.php'; ?>
