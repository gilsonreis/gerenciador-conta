<div>
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Visão Geral</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-8">Acompanhe a saúde financeira da sua família no mês atual.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-emerald-100 dark:border-emerald-900/30">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-400/80 uppercase tracking-wider mb-1">Entradas Previstas</p>
                <h3 class="text-3xl font-bold text-emerald-600 dark:text-emerald-500" id="dash-entradas">R$ 0,00</h3>
            </div>
            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 rounded-xl">
                <i class="fa-solid fa-arrow-trend-up text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-red-100 dark:border-red-900/30">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm font-medium text-red-800 dark:text-red-400/80 uppercase tracking-wider mb-1">Saídas Previstas</p>
                <h3 class="text-3xl font-bold text-red-600 dark:text-red-500" id="dash-saidas">R$ 0,00</h3>
            </div>
            <div class="p-3 bg-red-50 dark:bg-red-900/20 text-red-500 rounded-xl">
                <i class="fa-solid fa-arrow-trend-down text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-blue-100 dark:border-blue-900/30 relative overflow-hidden" id="dash-saldo-card">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-50 dark:bg-blue-900/10 rounded-full blur-3xl z-0 pointer-events-none transition-colors duration-500" id="dash-saldo-bg"></div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <p class="text-sm font-medium text-blue-800 dark:text-blue-400/80 uppercase tracking-wider mb-1" id="dash-saldo-label">Saldo do Mês</p>
                <h3 class="text-3xl font-bold text-blue-600 dark:text-blue-500 transition-colors duration-500" id="dash-saldo">R$ 0,00</h3>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-500 rounded-xl transition-colors duration-500" id="dash-saldo-icon">
                <i class="fa-solid fa-scale-balanced text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Custos de Vida Chart Area -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder flex flex-col items-center justify-center text-center min-h-[300px]">
        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-[#121212] flex items-center justify-center text-gray-500 mb-4 shadow-inner">
            <i class="fa-solid fa-shield-cat text-2xl"></i>
        </div>
        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Seu Custo de Vida Essencial</h4>
        <p class="text-gray-500 dark:text-gray-400 max-w-sm mb-4">Rastreie o quanto da sua renda está comprometida apenas pelo custo base de sobrevivência (Soma das despesas com flag de conta fixa).</p>
        <div class="text-3xl font-black text-gray-800 dark:text-gray-200" id="dash-custovida">R$ 0,00</div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-darkcard rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-darkborder">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Ações Rápidas</h4>
        
        <div class="space-y-4">
            <a href="?pagina=caixa-listar" class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-darkborder hover:border-secondary hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-all group">
                <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Nova Entrada</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Registrar salário, receitas, etc.</p>
                </div>
            </a>
            
            <a href="?pagina=despesas-listar" class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-darkborder hover:border-primary hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all group">
                <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-minus"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-800 dark:text-white group-hover:text-primary transition-colors">Nova Despesa</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Registrar boletos, cartão, etc.</p>
                </div>
            </a>
            
            <a href="?pagina=categorias-listar" class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-darkborder hover:border-gray-300 dark:hover:border-gray-600 transition-all group">
                <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center text-gray-600 dark:text-gray-400 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-800 dark:text-white transition-colors">Gerenciar Categorias</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Organizar as classificações dos gastos.</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
// Simple script embedded only for dashboard loads
$(document).ready(function() {
    function carregarDashboard() {
        $.ajax({
            url: 'ajax.php?acao=despesas-dashboard',
            method: 'GET',
            success: function(res) {
                if(res.sucesso) {
                    $('#dash-entradas').text(res.entradas_formatado);
                    $('#dash-saidas').text(res.saidas_formatado);
                    $('#dash-custovida').text(res.custovida_formatado);
                    
                    const saldoElem = $('#dash-saldo');
                    const saldoCard = $('#dash-saldo-card');
                    const saldoBg = $('#dash-saldo-bg');
                    const saldoIcon = $('#dash-saldo-icon');
                    const saldoLabel = $('#dash-saldo-label');
                    
                    saldoElem.text(res.saldo_formatado);
                    
                    // Cleanup previous state classes
                    saldoElem.removeClass('text-blue-600 dark:text-blue-500 text-emerald-600 dark:text-emerald-500 text-red-600 dark:text-red-500');
                    saldoCard.removeClass('border-blue-100 dark:border-blue-900/30 border-emerald-100 dark:border-emerald-900/30 border-red-100 dark:border-red-900/30');
                    saldoIcon.removeClass('bg-blue-50 dark:bg-blue-900/20 text-blue-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 bg-red-50 dark:bg-red-900/20 text-red-500');
                    
                    if (res.saldo >= 0) {
                        saldoElem.addClass('text-emerald-600 dark:text-emerald-500');
                        saldoCard.addClass('border-emerald-100 dark:border-emerald-900/30');
                        saldoIcon.addClass('bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500');
                        saldoLabel.addClass('text-emerald-800 dark:text-emerald-400/80').removeClass('text-blue-800 dark:text-blue-400/80');
                    } else {
                        saldoElem.addClass('text-red-600 dark:text-red-500');
                        saldoCard.addClass('border-red-100 dark:border-red-900/30');
                        saldoIcon.addClass('bg-red-50 dark:bg-red-900/20 text-red-500');
                        saldoLabel.addClass('text-red-800 dark:text-red-400/80').removeClass('text-blue-800 dark:text-blue-400/80');
                    }
                }
            }
        });
    }
    carregarDashboard();
});
</script>
