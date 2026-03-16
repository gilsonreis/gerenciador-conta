const categoriasJS = {
    carregar: function() {
        $.get('ajax.php?acao=categorias-listar', function(res) {
            let html = '';
            if(res.dados.length === 0) {
                html = '<tr><td colspan="2" class="p-8 text-center text-gray-500 dark:text-gray-400">Nenhuma categoria cadastrada.</td></tr>';
            } else {
                res.dados.forEach(c => {
                    html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-4 text-gray-900 dark:text-gray-100 font-medium">${c.nome}</td>
                        <td class="p-4 text-center">
                            <button onclick="categoriasJS.editar(${c.id})" class="text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded-lg transition-colors" title="Editar"><i class="fa-solid fa-pen"></i></button>
                            <button onclick="categoriasJS.excluir(${c.id})" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded-lg transition-colors ml-1" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tabela-categorias').html(html);
        });
    },
    abrirModalCadastro: function() {
        $('#form-categoria')[0].reset();
        $('#categoria_id').val('');
        $('#modal-categoria-title').text('Nova Categoria');
        this.mostrarModal();
    },
    editar: function(id) {
        $.get('ajax.php?acao=categorias-buscar', {id: id}, function(res) {
            $('#categoria_id').val(res.dados.id);
            $('#categoria_nome').val(res.dados.nome);
            $('#modal-categoria-title').text('Editar Categoria');
            categoriasJS.mostrarModal();
        });
    },
    excluir: function(id) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não pode ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, excluir!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax.php?acao=categorias-excluir', {id: id}, function(res) {
                    Swal.fire('Excluído!', res.mensagem, 'success');
                    categoriasJS.carregar();
                });
            }
        });
    },
    mostrarModal: function() {
        $('#modal-categoria-backdrop').removeClass('hidden');
        $('#modal-categoria').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modal-categoria-content').removeClass('scale-95 opacity-0');
        }, 10);
    },
    fecharModal: function() {
        $('#modal-categoria-content').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#modal-categoria-backdrop').addClass('hidden');
            $('#modal-categoria').addClass('hidden').removeClass('flex');
        }, 300);
    }
};

$(document).ready(function() {
    categoriasJS.carregar();
    
    $('#form-categoria').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-categoria');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Salvando...');

        $.post('ajax.php?acao=categorias-salvar', $(this).serialize(), function(res) {
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
            categoriasJS.fecharModal();
            categoriasJS.carregar();
        }).always(function() {
            btn.prop('disabled', false).html(originalHtml);
        });
    });
});
