const usuariosJS = {
    carregar: function() {
        $.get('ajax.php?acao=usuarios-listar', function(res) {
            let html = '';
            if(res.dados.length === 0) {
                html = '<tr><td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">Nenhum usuário cadastrado.</td></tr>';
            } else {
                // Mapa de badges por role
                const roleBadge = {
                    'super_admin': '<span class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 px-2 py-0.5 rounded-full text-xs font-semibold"><i class="fa-solid fa-crown text-[10px]"></i> Super Admin</span>',
                    'admin':       '<span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-0.5 rounded-full text-xs font-semibold"><i class="fa-solid fa-shield-halved text-[10px]"></i> Admin</span>',
                    'manager':     '<span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-0.5 rounded-full text-xs font-semibold"><i class="fa-solid fa-user-tie text-[10px]"></i> Gestor</span>',
                    'reader':      '<span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400 px-2 py-0.5 rounded-full text-xs font-semibold"><i class="fa-solid fa-eye text-[10px]"></i> Leitor</span>',
                };

                res.dados.forEach(u => {
                    const badge = roleBadge[u.role] || roleBadge['admin'];
                    html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 text-gray-900 dark:text-gray-100 font-medium">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-primary flex items-center justify-center font-bold text-xs uppercase">${u.nome.charAt(0)}</div>
                                <span>${u.nome}</span>
                            </div>
                        </td>
                        <td class="p-4 text-gray-500 dark:text-gray-400 text-sm hidden md:table-cell">
                            <span class="bg-indigo-100 text-indigo-700 dark:bg-white/5 dark:text-indigo-400 px-2 py-1 rounded text-xs font-medium"><i class="fa-solid fa-building mr-1"></i> ${u.instituicao_nome || 'N/A'}</span>
                        </td>
                        <td class="p-4 text-gray-500 dark:text-gray-400">${u.email}</td>
                        <td class="p-4">${badge}</td>
                        <td class="p-4 text-center">
                            <button onclick="usuariosJS.editar(${u.id})" class="text-blue-500 opacity-0 group-hover:opacity-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded-lg transition-all" title="Editar"><i class="fa-solid fa-pen"></i></button>
                            <button onclick="usuariosJS.excluir(${u.id})" class="text-red-500 opacity-0 group-hover:opacity-100 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded-lg transition-all ml-1" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tabela-usuarios').html(html);
        });
    },
    carregarInstituicoes: function(instituicaoAtiva, callback) {
        $.get('ajax.php?acao=instituicoes-listar', function(res) {
            let html = '<option value="">Selecione...</option>';
            res.dados.forEach(i => {
                html += `<option value="${i.id}">${i.nome}</option>`;
            });
            $('#usuario_instituicao').html(html);
            if (instituicaoAtiva) {
                $('#usuario_instituicao').val(instituicaoAtiva);
            }
            if(callback) callback();
        });
    },
    abrirModalCadastro: function() {
        const abrirModal = () => {
            $('#form-usuario')[0].reset();
            $('#usuario_id').val('');
            $('#modal-usuario-title').text('Novo Usuário');
            $('#usuario_senha').attr('required', true);
            $('#bloco-alertas').hide();
            $('#usuario_recebe_alertas').prop('checked', true);
            this.mostrarModal();
        };

        // Só carrega lista de instituições se o select existir (super_admin)
        if ($('#usuario_instituicao').length && $('#usuario_instituicao').is('select')) {
            this.carregarInstituicoes(null, abrirModal);
        } else {
            abrirModal();
        }
    },
    editar: function(id) {
        $.get('ajax.php?acao=usuarios-buscar', {id: id}, function(res) {
            const aplicarDados = () => {
                $('#usuario_id').val(res.dados.id);
                $('#usuario_nome').val(res.dados.nome);
                $('#usuario_email').val(res.dados.email);
                $('#usuario_senha').val('').removeAttr('required'); // Opcional ao editar

                // Pré-seleciona o perfil do usuário
                if ($('#usuario_role').length) {
                    $('#usuario_role').val(res.dados.role || 'admin');
                }

                // Exibe config de alertas recebida em buscar
                $('#bloco-alertas').show();
                $('#usuario_recebe_alertas').prop('checked', res.dados.recebe_alertas != 0);

                $('#modal-usuario-title').text('Editar Usuário');
                usuariosJS.mostrarModal();
            };

            // Só carrega instituições se o select existir (super_admin)
            if ($('#usuario_instituicao').length && $('#usuario_instituicao').is('select')) {
                usuariosJS.carregarInstituicoes(res.dados.instituicao_id, aplicarDados);
            } else {
                aplicarDados();
            }
        });
    },
    excluir: function(id) {
        Swal.fire({
            title: 'Excluir usuário?',
            text: "Cuidado, se ele for o único ou se tiver dados vinculados pode afetar a operação.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, excluir!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax.php?acao=usuarios-excluir', {id: id}, function(res) {
                    Swal.fire('Excluído!', res.mensagem, 'success');
                    usuariosJS.carregar();
                });
            }
        });
    },
    mostrarModal: function() {
        $('#modal-usuario-backdrop').removeClass('hidden');
        $('#modal-usuario').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modal-usuario-content').removeClass('scale-95 opacity-0');
        }, 10);
    },
    fecharModal: function() {
        $('#modal-usuario-content').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#modal-usuario-backdrop').addClass('hidden');
            $('#modal-usuario').addClass('hidden').removeClass('flex');
        }, 300);
    }
};

$(document).ready(function() {
    usuariosJS.carregar();
    
    $('#form-usuario').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-usuario');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Salvando...');

        $.post('ajax.php?acao=usuarios-salvar', $(this).serialize(), function(res) {
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
            usuariosJS.fecharModal();
            usuariosJS.carregar();
        }).always(function() {
            btn.prop('disabled', false).html(originalHtml);
        });
    });
});
