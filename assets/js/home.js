const homeJS = {
    init: function() {
        this.carregarDashboard();
        this.carregarSaldos();
        this.carregarGraficos();
    },

    carregarDashboard: function() {
        $.ajax({
            url: 'ajax.php?acao=despesas-dashboard',
            method: 'GET',
            success: function(res) {
                if(res.sucesso) {
                    $('#dash-saldo-atual').text(res.saldo_atual_formatado);
                    $('#dash-entradas-receber').text(res.entradas_receber_formatado);
                    $('#dash-total-caixa').text(res.total_caixa_formatado);
                    $('#dash-saidas-mes').text(res.saidas_mes_formatado);
                    $('#dash-custovida').text(res.custovida_formatado);
                    
                    const projElem = $('#dash-projecao-sobra');
                    const projCard = $('#dash-projecao-card');
                    const projBg = $('#dash-projecao-bg');
                    const projIcon = $('#dash-projecao-icon');
                    const projLabel = $('#dash-projecao-label');
                    
                    projElem.text(res.projecao_sobra_formatado);
                    
                    // Cleanup previous state classes
                    projElem.removeClass('text-emerald-600 dark:text-emerald-500 text-red-600 dark:text-red-500 text-gray-600 dark:text-gray-500');
                    projCard.removeClass('border-emerald-100 dark:border-emerald-900/30 border-red-100 dark:border-red-900/30 border-gray-100 dark:border-darkborder');
                    projIcon.removeClass('bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 bg-red-50 dark:bg-red-900/20 text-red-500 bg-gray-50 dark:bg-gray-900/20 text-gray-500');
                    projLabel.removeClass('text-emerald-800 dark:text-emerald-400/80 text-red-800 dark:text-red-400/80 text-gray-800 dark:text-gray-400');
                    projBg.removeClass('bg-emerald-50 dark:bg-emerald-900/10 bg-red-50 dark:bg-red-900/10 bg-gray-50 dark:bg-gray-900/10');
                    
                    if (res.projecao_sobra > 0) {
                        projElem.addClass('text-emerald-600 dark:text-emerald-500');
                        projCard.addClass('border-emerald-100 dark:border-emerald-900/30');
                        projIcon.addClass('bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500');
                        projLabel.addClass('text-emerald-800 dark:text-emerald-400/80');
                        projBg.addClass('bg-emerald-50 dark:bg-emerald-900/10');
                    } else if (res.projecao_sobra < 0) {
                        projElem.addClass('text-red-600 dark:text-red-500');
                        projCard.addClass('border-red-100 dark:border-red-900/30');
                        projIcon.addClass('bg-red-50 dark:bg-red-900/20 text-red-500');
                        projLabel.addClass('text-red-800 dark:text-red-400/80');
                        projBg.addClass('bg-red-50 dark:bg-red-900/10');
                    } else {
                        // Neutro (0)
                        projElem.addClass('text-gray-600 dark:text-gray-500');
                        projCard.addClass('border-gray-100 dark:border-darkborder');
                        projIcon.addClass('bg-gray-50 dark:bg-gray-900/20 text-gray-500');
                        projLabel.addClass('text-gray-800 dark:text-gray-400');
                        projBg.addClass('bg-gray-50 dark:bg-gray-900/10');
                    }
                }
            }
        });
    },

    carregarSaldos: function() {
        $.ajax({
            url: 'ajax.php?acao=contas-saldos',
            method: 'GET',
            success: function(res) {
                if(res.sucesso) {
                    let html = '';
                    if(res.dados.length === 0) {
                        html = '<tr><td colspan="2" class="text-center text-sm text-gray-500 py-8 border border-dashed border-gray-200 dark:border-darkborder rounded-xl">Nenhuma conta registrada.</td></tr>';
                    } else {
                        res.dados.forEach(c => {
                            const isNegativo = parseFloat(c.saldo_atual_real) < 0;
                            const corTexto = isNegativo ? 'text-red-500' : 'text-gray-800 dark:text-gray-200';
                            
                            html += `
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                                <td class="py-3 text-gray-700 dark:text-gray-300 font-medium text-sm flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full ${isNegativo ? 'bg-red-500' : 'bg-emerald-500'}"></div>
                                    <span class="truncate max-w-[140px]" title="${c.nome}">${c.nome}</span>
                                </td>
                                <td class="py-3 ${corTexto} text-right font-bold text-sm whitespace-nowrap font-mono">
                                    <span class="valor-sensivel">${c.saldo_atual_formatado}</span>
                                </td>
                            </tr>
                            `;
                        });
                    }
                    $('#contas-table').html(html);
                }
            }
        });
    },

    formatCurrency: function(value) {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
    },

    carregarGraficos: function() {
        $.ajax({
            url: 'ajax.php?acao=dashboard-graficos',
            method: 'GET',
            success: (res) => {
                if (res.sucesso) {
                    // Histórico (Lines)
                    const ctxHist = document.getElementById('chart-historico').getContext('2d');
                    new Chart(ctxHist, {
                        type: 'line',
                        data: {
                            labels: res.dados.historico.labels,
                            datasets: [
                                {
                                    label: 'Entradas',
                                    data: res.dados.historico.entradas,
                                    borderColor: '#10b981', // emerald-500
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Saídas (Pagas)',
                                    data: res.dados.historico.saidas,
                                    borderColor: '#ef4444', // red-500
                                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: (context) => this.formatCurrency(context.raw)
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        callback: (value) => this.formatCurrency(value)
                                    }
                                }
                            }
                        }
                    });

                    // Projeção (Bars)
                    const ctxProj = document.getElementById('chart-projecao').getContext('2d');
                    new Chart(ctxProj, {
                        type: 'bar',
                        data: {
                            labels: res.dados.projecao.labels,
                            datasets: [
                                {
                                    label: 'Saídas Previstas',
                                    data: res.dados.projecao.saidas,
                                    backgroundColor: '#f97316', // orange-500
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: (context) => this.formatCurrency(context.raw)
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (value) => this.formatCurrency(value)
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    }
};

$(document).ready(function() {
    homeJS.init();
});
