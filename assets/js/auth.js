$(document).ready(function() {
    $('#form-login').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const btn = $('#btn-login');
        
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Entrando...');

        $.ajax({
            url: 'ajax.php?acao=auth-login',
            method: 'POST',
            data: form.serialize(),
            success: function(res) {
                if (res.sucesso) {
                    window.location.href = 'index.php?pagina=home-index';
                }
            },
            error: function(xhr) {
                let msg = 'Erro ao processar o login.';
                if (xhr.responseJSON && xhr.responseJSON.erro) {
                    msg = xhr.responseJSON.erro;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Ops...',
                    text: msg
                });
            },
            complete: function() {
                // Em caso de catch ou sucesso, libera botao (caso falhe o redirect por ex)
                btn.prop('disabled', false).html('Entrar');
            }
        });
    });
});
