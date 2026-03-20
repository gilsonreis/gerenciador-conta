const transferenciasJS = {
    carregarContas: function(instId) {
        const params = instId ? {instituicao_id: instId} : {};
        $.get('ajax.php?acao=contas-listar', params, function(res) {
            if (!res.dados) return;
            let html = '<option value="">Selecione a conta...</option>';
            res.dados.forEach(c => {
                html += `<option value="${c.id}">${c.nome}</option>`;
            });
            $('#conta_origem_id, #conta_destino_id').html(html);
        });
    },

    carregarInstituicoes: function(callback) {
        $.get('ajax.php?acao=instituicoes-listar', function(res) {
            let html = '<option value="">Selecione...</option>';
            (res.dados || []).forEach(i => {
                html += `<option value="${i.id}">${i.nome}</option>`;
            });
            $('#transferencia_instituicao_id').html(html);
            if (callback) callback();
        });
    },

    carregarContasDaInstituicao: function(instId) {
        $('#conta_origem_id, #conta_destino_id').html('<option value="">Selecione a conta...</option>');
        if (instId) this.carregarContas(instId);
    },

    listar: function() {
        const cols = window.userRole === 'super_admin' ? 6 : 5;
        $('#tabela-transferencias').html(`<tr><td colspan="${cols}" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>`);

        $.get('ajax.php?acao=transferencias-listar', function(res) {
            let html = '';
            if (res.dados.length === 0) {
                html = `<tr><td colspan="${cols}" class="p-8 text-center text-gray-500 font-medium">Nenhuma transferência realizada ainda.</td></tr>`;
            } else {
                res.dados.forEach(t => {
                    const ctxCell = res.is_super_admin
                        ? `<td class="p-4 text-xs text-gray-500 dark:text-gray-400">
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-white/10 rounded-lg">
                                <i class="fa-solid fa-building text-xs"></i> ${t.instituicao_nome || '—'}
                              </span>
                           </td>`
                        : '';
                    html += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap">${t.data_formatada}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded">Saiu</span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">${t.conta_origem_nome}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs font-bold px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded">Entrou</span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">${t.conta_destino_nome}</span>
                                </div>
                            </td>
                            <td class="p-4 text-sm text-gray-600 dark:text-gray-400 italic">${t.descricao}</td>
                            <td class="p-4 text-right font-bold text-gray-900 dark:text-white whitespace-nowrap valor-sensivel">${t.valor_formatado}</td>
                            ${ctxCell}
                            <td class="p-4 text-center">
                                <button onclick="transferenciasJS.excluir(${t.id})" class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 opacity-0 group-hover:opacity-100">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#tabela-transferencias').html(html);
        });
    },

    abrirModal: function() {
        $('#form-transferencia')[0].reset();
        $('#transferencia_data').val(new Date().toISOString().substring(0, 10));

        if ($('#transferencia_instituicao_id').length) {
            // Super admin: carrega instituções; contas carregam ao selecionar instituição
            this.carregarInstituicoes(() => {
                $('#conta_origem_id, #conta_destino_id').html('<option value="">Selecione a conta...</option>');
            });
        } else {
            this.carregarContas();
        }

        $('#modal-transferencia-backdrop').removeClass('hidden');
        $('#modal-transferencia').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modal-transferencia-content').removeClass('scale-95 opacity-0');
        }, 10);
    },

    fecharModal: function() {
        $('#modal-transferencia-content').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#modal-transferencia-backdrop').addClass('hidden');
            $('#modal-transferencia').addClass('hidden').removeClass('flex');
        }, 300);
    },

    salvar: function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-transferencia');
        const originalHtml = btn.html();

        if ($('#conta_origem_id').val() === $('#conta_destino_id').val()) {
            Swal.fire('Erro', 'As contas de origem e destino devem ser diferentes.', 'warning');
            return;
        }

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Processando...');

        $.post('ajax.php?acao=transferencias-salvar', $('#form-transferencia').serialize(), function(res) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.mensagem,
                showConfirmButton: false,
                timer: 3000
            });
            transferenciasJS.fecharModal();
            transferenciasJS.listar();
        }, 'json').always(() => {
            btn.prop('disabled', false).html(originalHtml);
        });
    },

    excluir: function(id) {
        Swal.fire({
            title: 'Excluir Transferência?',
            text: "Os saldos das contas envolvidas serão estornados automaticamente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax.php?acao=transferencias-excluir', { id: id }, function(res) {
                    Swal.fire('Excluída!', res.mensagem, 'success');
                    transferenciasJS.listar();
                }, 'json');
            }
        });
    }
};

$(document).ready(function() {
    if ($('#tabela-transferencias').length) {
        transferenciasJS.listar();
    }

    $('#form-transferencia').on('submit', transferenciasJS.salvar);
});
