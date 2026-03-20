const contasJS = {
    carregar: function() {
        const cols = window.userRole === 'super_admin' ? 4 : 3;
        $('#tabela-contas').html(`<tr><td colspan="${cols}" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>`);

        $.get('ajax.php?acao=contas-listar', function(res) {
            let html = '';
            if(res.dados.length === 0) {
                html = `<tr><td colspan="${cols}" class="p-8 text-center text-gray-500 dark:text-gray-400">Nenhuma conta/carteira cadastrada nesta institução.</td></tr>`;
            } else {
                res.dados.forEach(d => {
                    const ctxCell = res.is_super_admin
                        ? `<td class="p-4 text-xs text-gray-500 dark:text-gray-400">
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-white/10 rounded-lg">
                                <i class="fa-solid fa-building text-xs"></i> ${d.instituicao_nome || '—'}
                              </span>
                           </td>`
                        : '';
                    html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 text-gray-900 dark:text-gray-100 font-medium">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                ${d.nome}
                            </div>
                        </td>
                        <td class="p-4 text-right text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap">
                            R$ <span class="valor-sensivel">${d.saldo_inicial_formatado}</span>
                        </td>
                        ${ctxCell}
                        <td class="p-4 text-center">
                            <button onclick="contasJS.editar(${d.id})" class="text-blue-500 opacity-0 group-hover:opacity-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded-lg transition-all" title="Editar"><i class="fa-solid fa-pen"></i></button>
                            <button onclick="contasJS.excluir(${d.id})" class="text-red-500 opacity-0 group-hover:opacity-100 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded-lg transition-all ml-1" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tabela-contas').html(html);
        });
    },

    carregarInstituicoes: function(selectedId, callback) {
        $.get('ajax.php?acao=instituicoes-listar', function(res) {
            let html = '<option value="">Selecione...</option>';
            (res.dados || []).forEach(i => {
                const sel = (selectedId && i.id == selectedId) ? 'selected' : '';
                html += `<option value="${i.id}" ${sel}>${i.nome}</option>`;
            });
            $('#conta_instituicao_id').html(html);
            if (callback) callback();
        });
    },

    abrirModalCadastro: function() {
        const abrir = () => {
            $('#form-conta')[0].reset();
            $('#conta_id').val('');
            $('#modal-conta-title').text('Nova Conta');
            this.mostrarModal();
        };
        if ($('#conta_instituicao_id').length) {
            this.carregarInstituicoes(null, abrir);
        } else {
            abrir();
        }
    },

    editar: function(id) {
        $.get('ajax.php?acao=contas-buscar', {id: id}, function(res) {
            if(res.sucesso) {
                const aplicar = () => {
                    $('#conta_id').val(res.dados.id);
                    $('#conta_nome').val(res.dados.nome);

                    let saldoFormatado = '';
                    if(res.dados.saldo_inicial) {
                        saldoFormatado = parseFloat(res.dados.saldo_inicial).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                    $('#conta_saldo').val(saldoFormatado);

                    $('#modal-conta-title').text('Editar Conta');
                    contasJS.mostrarModal();
                };
                if ($('#conta_instituicao_id').length) {
                    contasJS.carregarInstituicoes(res.dados.instituicao_id, aplicar);
                } else {
                    aplicar();
                }
            }
        });
    },

    excluir: function(id) {
        Swal.fire({
            title: 'Excluir carteira?',
            text: "Você tem certeza que quer excluir esta Conta? Todos os Lançamentos e Caixas com referência direta a ela podem ser afetados ou bloqueados de remoção.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, excluir!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax.php?acao=contas-excluir', {id: id}, function(res) {
                    Swal.fire('Excluída!', res.mensagem, 'success');
                    contasJS.carregar();
                });
            }
        });
    },

    mostrarModal: function() {
        $('#modal-conta-backdrop').removeClass('hidden');
        $('#modal-conta').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modal-conta-content').removeClass('scale-95 opacity-0');
        }, 10);
    },
    
    fecharModal: function() {
        $('#modal-conta-content').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#modal-conta-backdrop').addClass('hidden');
            $('#modal-conta').addClass('hidden').removeClass('flex');
        }, 300);
    }
};

$(document).ready(function() {
    if($('#tabela-contas').length) {
        contasJS.carregar();
    }
    
    $('#form-conta').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-conta');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Salvando...');

        $.post('ajax.php?acao=contas-salvar', $(this).serialize(), function(res) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.mensagem,
                showConfirmButton: false,
                timer: 3000
            });
            contasJS.fecharModal();
            contasJS.carregar();
        }).always(function() {
            btn.prop('disabled', false).html(originalHtml);
        });
    });
});
