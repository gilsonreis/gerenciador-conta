const snapshotsJS = {
    todosOsDados: [],

    init: function() {
        this.carregar();
    },

    carregar: function() {
        $.ajax({
            url: 'ajax.php?acao=snapshots-listar',
            method: 'GET',
            success: (res) => {
                if (!res.sucesso) return;

                this.todosOsDados = res.dados;

                // Popula filtro de anos
                const select = $('#filtro-ano');
                select.find('option:not(:first)').remove();
                res.anos.forEach(ano => {
                    select.append(`<option value="${ano}">${ano}</option>`);
                });

                this.renderizar(res.dados);
            },
            error: function() {
                $('#tabela-snapshots').html(`
                    <tr>
                        <td colspan="4" class="py-12 text-center text-sm text-red-500">
                            Erro ao carregar os snapshots.
                        </td>
                    </tr>`);
            }
        });
    },

    filtrar: function() {
        const ano = $('#filtro-ano').val();
        const filtrados = ano
            ? this.todosOsDados.filter(s => s.mes_referencia.startsWith(ano))
            : this.todosOsDados;
        this.renderizar(filtrados);
    },

    renderizar: function(dados) {
        const tbody = $('#tabela-snapshots');
        $('#snapshots-count').text(`${dados.length} registro${dados.length !== 1 ? 's' : ''}`);

        if (dados.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="4" class="py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center">
                                <i class="fa-solid fa-database text-gray-300 dark:text-gray-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nenhum snapshot encontrado</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Execute o cron de snapshots ou o migration seed para popular este histórico.</p>
                            </div>
                        </div>
                    </td>
                </tr>`);
            return;
        }

        // Agrupa por mês para facilitar a leitura visual
        let htmlRows = '';
        let ultimoMes = null;

        dados.forEach(s => {
            const isNovoMes = s.mes_referencia !== ultimoMes;
            ultimoMes = s.mes_referencia;

            // Cor por valor
            const isZero = s.valor_abertura === 0;
            const corValor = isZero
                ? 'text-gray-400 dark:text-gray-600'
                : 'text-emerald-600 dark:text-emerald-400 font-bold';

            htmlRows += `
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors ${isNovoMes ? 'border-t-2 border-gray-200 dark:border-darkborder/80' : ''}">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-building-columns text-primary text-xs"></i>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-sm">${s.conta_nome}</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/20 text-primary dark:text-blue-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                            <i class="fa-solid fa-calendar-day text-[10px]"></i>
                            ${s.mes_formatado}
                        </span>
                    </td>
                    <td class="p-4 text-right font-mono text-sm ${corValor}">
                        ${s.valor_formatado}
                    </td>
                    <td class="p-4 text-center hidden sm:table-cell">
                        <span class="text-xs text-gray-400 dark:text-gray-500">${s.criado_em}</span>
                    </td>
                </tr>`;
        });

        tbody.html(htmlRows);
    }
};

$(document).ready(function() {
    snapshotsJS.init();
});
