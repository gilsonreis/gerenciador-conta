const homeJS = {
    init: function() {
        this.carregarDashboard();
        this.carregarSaldos();
        this.carregarVencimentos();
        this.carregarGraficos();
    },

    carregarDashboard: function() {
        $.ajax({
            url: 'ajax.php?acao=despesas-dashboard',
            method: 'GET',
            success: function(res) {
                if(res.sucesso) {
                    $('#dash-saldo-anterior').text(res.saldo_base_formatado.replace('R$ ', ''));
                    $('#dash-entradas-mes-card').text(res.entradas_mes_formatado.replace('R$ ', ''));
                    $('#dash-total-agora').text(res.total_disponivel_formatado.replace('R$ ', ''));
                    $('#dash-saidas-mes').text(res.saidas_mes_formatado);
                    $('#dash-custovida').text(res.custovida_formatado);

                    // Tooltip: Composição do Saldo de Abertura (por conta)
                    if (res.tooltip_abertura && res.tooltip_abertura.length) {
                        let htmlAbertura = '';
                        res.tooltip_abertura.forEach(item => {
                            htmlAbertura += `
                                <div class="flex justify-between items-center gap-3">
                                    <span class="text-gray-400 truncate">${item.conta}</span>
                                    <span class="text-white font-mono font-semibold whitespace-nowrap">${item.valor}</span>
                                </div>`;
                        });
                        $('#tooltip-abertura-list').html(htmlAbertura);
                    }

                    // Tooltip: Top 5 Entradas do Mês
                    if (res.tooltip_entradas && res.tooltip_entradas.length) {
                        let htmlEntradas = '';
                        res.tooltip_entradas.forEach(item => {
                            htmlEntradas += `
                                <div class="flex justify-between items-center gap-3">
                                    <span class="text-gray-400 truncate">${item.descricao}</span>
                                    <span class="text-emerald-400 font-mono font-semibold whitespace-nowrap">${item.valor}</span>
                                </div>`;
                        });
                        if (res.tooltip_entradas.length === 5) {
                            htmlEntradas += `<p class="text-gray-500 text-[10px] mt-2 text-center">Exibindo top 5 por valor</p>`;
                        }
                        $('#tooltip-entradas-list').html(htmlEntradas);
                    }

                    // Card 1 Dynamic Coloring
                    const capCard = $('#dash-capital-card');
                    const capIcon = $('#dash-capital-icon');
                    const capValue = $('#dash-capital-value-container');
                    
                    // Cleanup Card 1
                    capCard.removeClass('border-emerald-100 dark:border-emerald-900/30 border-red-100 dark:border-red-900/30 border-gray-100 dark:border-darkborder');
                    capIcon.removeClass('bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 bg-red-50 dark:bg-red-900/20 text-red-500 bg-gray-50 dark:bg-gray-900/20 text-gray-500');
                    capValue.removeClass('text-emerald-600 dark:text-emerald-500 text-red-600 dark:text-red-500 text-gray-600 dark:text-gray-500');
                    
                    if (res.total_disponivel > 0) {
                        capCard.addClass('border-emerald-100 dark:border-emerald-900/30');
                        capIcon.addClass('bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500');
                        capValue.addClass('text-emerald-600 dark:text-emerald-500');
                    } else if (res.total_disponivel < 0) {
                        capCard.addClass('border-red-100 dark:border-red-900/30');
                        capIcon.addClass('bg-red-50 dark:bg-red-900/20 text-red-500');
                        capValue.addClass('text-red-600 dark:text-red-500');
                    } else {
                        capCard.addClass('border-gray-100 dark:border-darkborder');
                        capIcon.addClass('bg-gray-50 dark:bg-gray-900/20 text-gray-500');
                        capValue.addClass('text-gray-600 dark:text-gray-500');
                    }

                    // Card 3 Dynamic Coloring
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
    },
    carregarVencimentos: function() {
        $.ajax({
            url: 'ajax.php?acao=despesas-proximos_vencimentos',
            method: 'GET',
            success: function(res) {
                if (!res.sucesso) return;

                const tbody = $('#vencimentos-table');

                if (res.dados.length === 0) {
                    tbody.html(`
                        <tr>
                            <td colspan="3" class="py-8 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-400 text-2xl"></i>
                                    <span class="text-sm text-gray-400 dark:text-gray-500">Nenhum vencimento próximo.<br>Tudo limpo!</span>
                                </div>
                            </td>
                        </tr>`);
                    return;
                }

                // Mapa de urgência → classes Tailwind
                const urgenciaCores = {
                    'hoje':    { badge: 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',   valor: 'text-red-600 dark:text-red-400' },
                    'critico': { badge: 'bg-red-100 dark:bg-red-900/30 text-red-500 dark:text-red-400',   valor: 'text-red-500 dark:text-red-400' },
                    'alerta':  { badge: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400', valor: 'text-orange-500 dark:text-orange-400' },
                    'normal':  { badge: 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400',   valor: 'text-gray-700 dark:text-gray-300' },
                };

                let html = '';
                res.dados.forEach(v => {
                    const cores = urgenciaCores[v.urgencia] || urgenciaCores['normal'];
                    const diasLabel = v.dias_restantes === 0
                        ? '<span class="font-bold">Hoje</span>'
                        : v.data_formatada;

                    html += `
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="py-2.5 pr-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium truncate block max-w-[130px]" title="${v.descricao}">${v.descricao}</span>
                                <span class="text-xs text-gray-400">${v.categoria}</span>
                            </td>
                            <td class="py-2.5 px-1">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap ${cores.badge}">${diasLabel}</span>
                            </td>
                            <td class="py-2.5 text-right font-mono font-bold text-sm whitespace-nowrap ${cores.valor}">
                                ${v.valor_formatado}
                            </td>
                        </tr>`;
                });

                tbody.html(html);
            }
        });
    }
};

$(document).ready(function() {
    homeJS.init();
});
