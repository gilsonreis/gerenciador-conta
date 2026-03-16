const instituicoesJS = {
    carregar: function() {
        $.get('ajax.php?acao=instituicoes-listar', function(res) {
            let html = '';
            if(res.dados.length === 0) {
                html = '<tr><td colspan="2" class="p-8 text-center text-gray-500 dark:text-gray-400">Nenhuma instituição encontrada.</td></tr>';
            } else {
                res.dados.forEach(i => {
                    html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 text-gray-900 dark:text-gray-100 font-medium">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs uppercase">${i.nome.charAt(0)}</div>
                                <span>${i.nome}</span>
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <button onclick="instituicoesJS.editar(${i.id})" class="text-blue-500 opacity-0 group-hover:opacity-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded-lg transition-all" title="Editar"><i class="fa-solid fa-pen"></i></button>
                            <button onclick="instituicoesJS.excluir(${i.id})" class="text-red-500 opacity-0 group-hover:opacity-100 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded-lg transition-all ml-1" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tabela-instituicoes').html(html);
        });
    },
    abrirModalCadastro: function() {
        $('#form-instituicao')[0].reset();
        $('#instituicao_id').val('');
        $('#modal-instituicao-title').text('Nova Instituição');
        this.mostrarModal();
    },
    editar: function(id) {
        $.get('ajax.php?acao=instituicoes-buscar', {id: id}, function(res) {
            $('#instituicao_id').val(res.dados.id);
            $('#instituicao_nome').val(res.dados.nome);
            $('#modal-instituicao-title').text('Editar Instituição');
            instituicoesJS.mostrarModal();
        });
    },
    excluir: function(id) {
        Swal.fire({
            title: 'Excluir Instituição?',
            text: "Cuidado: Dados vinculados podem impedir a exclusão. Você não pode excluir a instituição logada no momento.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, tentar excluir!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax.php?acao=instituicoes-excluir', {id: id}, function(res) {
                    Swal.fire('Excluído!', res.mensagem, 'success');
                    instituicoesJS.carregar();
                });
            }
        });
    },
    mostrarModal: function() {
        $('#modal-instituicao-backdrop').removeClass('hidden');
        $('#modal-instituicao').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modal-instituicao-content').removeClass('scale-95 opacity-0');
        }, 10);
    },
    fecharModal: function() {
        $('#modal-instituicao-content').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#modal-instituicao-backdrop').addClass('hidden');
            $('#modal-instituicao').addClass('hidden').removeClass('flex');
        }, 300);
    }
};

$(document).ready(function() {
    if($('#tabela-instituicoes').length) {
        instituicoesJS.carregar();
    }
    
    $('#form-instituicao').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-instituicao');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Salvando...');

        $.post('ajax.php?acao=instituicoes-salvar', $(this).serialize(), function(res) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.mensagem,
                showConfirmButton: false,
                timer: 3000,
                background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#111827'
            });
            instituicoesJS.fecharModal();
            instituicoesJS.carregar();
        }).always(function() {
            btn.prop('disabled', false).html(originalHtml);
        });
    });
});
