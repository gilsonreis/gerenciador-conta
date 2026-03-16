<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-users text-primary"></i> Usuários
        </h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gerencie quem tem acesso ao controle familiar.</p>
    </div>
    <button onclick="usuariosJS.abrirModalCadastro()" class="bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-user-plus"></i> Novo Usuário
    </button>
</div>

<!-- Table / List -->
<div class="bg-white dark:bg-darkcard rounded-xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="overflow-x-auto" style="min-height: 200px;">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wider">
                    <th class="p-4 font-semibold">Nome</th>
                    <th class="p-4 font-semibold">Instituição</th>
                    <th class="p-4 font-semibold">E-mail</th>
                    <th class="p-4 font-semibold w-32 text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-usuarios" class="divide-y divide-gray-100 dark:divide-darkborder">
                <!-- Preenchido via AJAX -->
                <tr><td colspan="4" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/_form.inc.php'; ?>
