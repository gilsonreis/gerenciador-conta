<!-- ============================================================ -->
<!-- PAINEL SUPER ADMIN — Visão SaaS / Saúde do Sistema          -->
<!-- ============================================================ -->
<div id="saas-dashboard">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                <i class="fa-solid fa-crown text-white text-sm"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Painel de Controle</h2>
                <p class="text-sm text-purple-500 dark:text-purple-400 font-medium">Super Admin · Visão Global do Sistema</p>
            </div>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Métricas consolidadas de todas as instituições cadastradas.</p>
    </div>

    <!-- 3 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

        <!-- Card 1: Instituições -->
        <div class="relative overflow-hidden bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder group transition-all hover:shadow-md hover:-translate-y-0.5 duration-200">
            <div class="absolute -right-6 -top-6 w-28 h-28 bg-blue-100/60 dark:bg-blue-500/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Instituições Ativas</p>
                    <p class="text-4xl font-black text-gray-800 dark:text-white" id="saas-total-instituicoes">—</p>
                    <p class="text-xs text-gray-400 mt-1">famílias cadastradas</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-building text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Usuários -->
        <div class="relative overflow-hidden bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder group transition-all hover:shadow-md hover:-translate-y-0.5 duration-200">
            <div class="absolute -right-6 -top-6 w-28 h-28 bg-emerald-100/60 dark:bg-emerald-500/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Usuários Cadastrados</p>
                    <p class="text-4xl font-black text-gray-800 dark:text-white" id="saas-total-usuarios">—</p>
                    <p class="text-xs text-gray-400 mt-1">usuários ativos</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-users text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Volume de Registros -->
        <div class="relative overflow-hidden bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder group transition-all hover:shadow-md hover:-translate-y-0.5 duration-200">
            <div class="absolute -right-6 -top-6 w-28 h-28 bg-amber-100/60 dark:bg-amber-500/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Volume de Registros</p>
                    <p class="text-4xl font-black text-gray-800 dark:text-white" id="saas-total-lancamentos">—</p>
                    <p class="text-xs text-gray-400 mt-1">parcelas / lançamentos</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-database text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabela: Últimos Usuários Cadastrados -->
    <div class="bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Últimos Usuários Cadastrados</h3>
            </div>
            <span class="text-xs text-gray-400">5 mais recentes</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-darkborder">
                        <th class="px-6 py-3 font-semibold">Usuário</th>
                        <th class="px-6 py-3 font-semibold">E-mail</th>
                        <th class="px-6 py-3 font-semibold">Perfil</th>
                        <th class="px-6 py-3 font-semibold">Instituição</th>
                        <th class="px-6 py-3 font-semibold">Cadastrado em</th>
                    </tr>
                </thead>
                <tbody id="saas-usuarios-recentes" class="divide-y divide-gray-100 dark:divide-darkborder">
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
// Carrega as métricas SaaS via AJAX
(function loadSaasMetrics() {
    $.get('ajax.php?acao=dashboard-saas_metrics', function(res) {
        if (!res.sucesso) return;

        const m = res.metricas;
        $('#saas-total-instituicoes').text(m.total_instituicoes);
        $('#saas-total-usuarios').text(m.total_usuarios);
        $('#saas-total-lancamentos').text(m.total_lancamentos_formatado);

        const roleBadge = {
            admin:   '<span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded text-[10px] font-bold uppercase">Admin</span>',
            manager: '<span class="px-2 py-0.5 bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-400 rounded text-[10px] font-bold uppercase">Gestor</span>',
            reader:  '<span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 rounded text-[10px] font-bold uppercase">Leitor</span>',
        };

        let html = '';
        if (res.usuarios_recentes.length === 0) {
            html = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Nenhum usuário encontrado.</td></tr>';
        } else {
            res.usuarios_recentes.forEach(u => {
                const badge = roleBadge[u.role] || `<span class="px-2 py-0.5 bg-gray-200 text-gray-600 rounded text-[10px]">${u.role}</span>`;
                const dataCadastro = u.criado_em
                    ? new Date(u.criado_em).toLocaleDateString('pt-BR')
                    : '—';
                const iniciais = u.nome.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
                html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold shrink-0">${iniciais}</div>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">${u.nome}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">${u.email}</td>
                    <td class="px-6 py-3">${badge}</td>
                    <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">${u.instituicao_nome}</td>
                    <td class="px-6 py-3 text-sm text-gray-400">${dataCadastro}</td>
                </tr>`;
            });
        }
        $('#saas-usuarios-recentes').html(html);
    }).fail(function() {
        $('#saas-usuarios-recentes').html('<tr><td colspan="5" class="px-6 py-8 text-center text-red-400">Erro ao carregar métricas.</td></tr>');
    });
})();
</script>
