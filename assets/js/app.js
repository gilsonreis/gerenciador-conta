$(document).ready(function() {
    // Inicialização global de máscaras
    $('.moeda_brl').mask('#.##0,00', {reverse: true});

    // Interceptador global para requisicoes AJAX
    $.ajaxSetup({
        error: function(jqXHR, textStatus, errorThrown) {
            let errorMsg = 'Ocorreu um erro interno no servidor.';
            let redirectUrl = null;

            if (jqXHR.responseJSON && jqXHR.responseJSON.erro) {
                errorMsg = jqXHR.responseJSON.erro;
                if (jqXHR.responseJSON.redirect) {
                    redirectUrl = jqXHR.responseJSON.redirect;
                }
            } else if (jqXHR.status === 401 || jqXHR.status === 403) {
                redirectUrl = 'login.php';
            }

            // Exceção: ignoramos avisos globais se a requisição tratou por si mesma? 
            // Para mantermos simples, exibiremos o SweetAlert.
            
            Swal.fire({
                icon: 'error',
                title: 'Ops...',
                text: errorMsg,
                confirmButtonColor: '#3b82f6',
                background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#111827'
            }).then(() => {
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            });
        }
    });

    // Toggle da Sidebar Mobile
    const btnMobile = $('#mobile-menu-btn');
    const overlayMobile = $('#mobile-menu-overlay');
    const sidebarMobile = $('#mobile-menu-sidebar');
    const closeMobile = $('#mobile-menu-close');

    if (btnMobile.length) {
        btnMobile.on('click', function() {
            overlayMobile.removeClass('hidden');
            setTimeout(() => sidebarMobile.removeClass('-translate-x-full'), 10);
        });

        const fecharMenuMenu = () => {
            sidebarMobile.addClass('-translate-x-full');
            setTimeout(() => overlayMobile.addClass('hidden'), 300);
        };

        closeMobile.on('click', fecharMenuMenu);
        overlayMobile.on('click', fecharMenuMenu);
    }
    
    // Configuração do Modo Privacidade
    const btnPrivacidade = $('.btn-toggle-privacidade');
    const iconesPrivacidade = btnPrivacidade.find('i');
    
    // Ao iniciar a página, verificar o localStorage
    if (localStorage.getItem('modo_privacidade') === 'ativo') {
        $('body').addClass('modo-privacidade');
        iconesPrivacidade.removeClass('fa-eye').addClass('fa-eye-slash text-blue-500');
    }
    
    // Evento de clique para todos os botões (desktop e mobile)
    btnPrivacidade.on('click', function() {
        $('body').toggleClass('modo-privacidade');
        
        if ($('body').hasClass('modo-privacidade')) {
            localStorage.setItem('modo_privacidade', 'ativo');
            iconesPrivacidade.removeClass('fa-eye').addClass('fa-eye-slash text-blue-500');
        } else {
            localStorage.setItem('modo_privacidade', 'inativo');
            iconesPrivacidade.removeClass('fa-eye-slash text-blue-500').addClass('fa-eye');
        }
    });

});
