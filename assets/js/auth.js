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
            complete: function() {
                // Em caso de catch ou sucesso, libera botao (caso falhe o redirect por ex)
                btn.prop('disabled', false).html('Entrar');
            }
        });
    });
});
