const contasJS = {
    carregar: function() {
        $('#tabela-contas').html('<tr><td colspan="3" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>');
        
        $.get('ajax.php?acao=contas-listar', function(res) {
            let html = '';
            if(res.dados.length === 0) {
                html = '<tr><td colspan="3" class="p-8 text-center text-gray-500 dark:text-gray-400">Nenhuma conta/carteira cadastrada nesta instituição.</td></tr>';
            } else {
                res.dados.forEach(d => {
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
                        <td class="p-4 text-right text-gray-500 dark:text-gray-400 font-medium">
                            R$ ${d.saldo_inicial_formatado}
                        </td>
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

    abrirModalCadastro: function() {
        $('#form-conta')[0].reset();
        $('#conta_id').val('');
        $('#modal-conta-title').text('Nova Conta');
        this.mostrarModal();
    },

    editar: function(id) {
        $.get('ajax.php?acao=contas-buscar', {id: id}, function(res) {
            if(res.sucesso) {
                $('#conta_id').val(res.dados.id);
                $('#conta_nome').val(res.dados.nome);
                
                let saldoFormatado = '';
                if(res.dados.saldo_inicial) {
                    saldoFormatado = parseFloat(res.dados.saldo_inicial).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
                $('#conta_saldo').val(saldoFormatado);
                
                $('#modal-conta-title').text('Editar Conta');
                contasJS.mostrarModal();
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
