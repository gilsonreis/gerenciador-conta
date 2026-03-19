const perfilJS = {
    abrir: function() {
        $.ajax({
            url: 'ajax.php?acao=usuarios-perfil',
            method: 'GET',
            success: (res) => {
                if (!res.sucesso) return;
                const u = res.dados;

                $('#perfil-email').text(u.email);
                $('#perfil_nome').val(u.nome);
                $('#perfil_senha').val('');
                $('#perfil_recebe_alertas').prop('checked', u.recebe_alertas != 0);
                $('#perfil-avatar-inicial').text(u.nome.charAt(0).toUpperCase());

                // Badge do role
                const roles = {
                    'super_admin': '👑 Super Admin',
                    'admin':       '🛡️ Admin',
                    'manager':     '👔 Gestor',
                    'reader':      '👁️ Leitor',
                };
                $('#perfil-role-badge').text(roles[window.userRole] || window.userRole || '');

                // Mostra modal
                const modal   = $('#modal-perfil');
                const backdrop = $('#modal-perfil-backdrop');
                modal.removeClass('hidden').addClass('flex');
                backdrop.removeClass('hidden');
                setTimeout(() => {
                    modal.find('>div').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
                }, 10);
            },
            error: () => {
                Swal.fire('Erro', 'Não foi possível carregar os dados do perfil.', 'error');
            }
        });
    },

    fechar: function() {
        const modal   = $('#modal-perfil');
        const backdrop = $('#modal-perfil-backdrop');
        modal.find('>div').addClass('scale-95 opacity-0').removeClass('scale-100 opacity-100');
        setTimeout(() => {
            modal.addClass('hidden').removeClass('flex');
            backdrop.addClass('hidden');
        }, 200);
    },

    salvar: function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-perfil');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Salvando...');

        $.ajax({
            url: 'ajax.php?acao=usuarios-perfil',
            method: 'POST',
            data: $('#form-perfil').serialize(),
            success: (res) => {
                if (res.sucesso) {
                    // Atualiza nome no sidebar sem recarregar a página
                    $('#sidebar-usuario-nome').text(res.nome);
                    $('#perfil-avatar-inicial').text(res.nome.charAt(0).toUpperCase());
                    perfilJS.fechar();
                    Swal.fire({
                        icon: 'success',
                        title: 'Perfil atualizado!',
                        timer: 1800,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                    });
                } else {
                    Swal.fire('Erro', res.erro || 'Falha ao salvar.', 'error');
                }
            },
            error: (xhr) => {
                const err = xhr.responseJSON?.erro || 'Erro inesperado.';
                Swal.fire('Erro', err, 'error');
            },
            complete: () => {
                btn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Salvar');
            }
        });
    }
};

$(document).ready(function() {
    // Fecha ao clicar fora
    $('#modal-perfil-backdrop').on('click', () => perfilJS.fechar());
    // Submit do form
    $('#form-perfil').on('submit', perfilJS.salvar);
});
