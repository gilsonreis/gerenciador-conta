const despesasJS = {
    mesAtual: new Date().toISOString().substring(0, 7),
    dadosOriginais: [], // Para filtragem no frontend
    filtroAtual: 'todas',
    
    mudarMes: function(offset) {
        let [ano, mes] = this.mesAtual.split('-');
        let data = new Date(ano, mes - 1 + offset, 1);
        this.mesAtual = data.toISOString().substring(0, 7);
        this.carregar();
    },

    formatarMesAno: function(anoMes) {
        const [ano, mes] = anoMes.split('-');
        const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        return `${meses[parseInt(mes)-1]} ${ano}`;
    },

    formatarDataBR: function(dataISO) {
        const [ano, mes, dia] = dataISO.split(' ')[0].split('-');
        return `${dia}/${mes}/${ano}`;
    },

    formatarMoeda: function(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },

    carregar: function() {
        $('#mes-atual-label').text(this.formatarMesAno(this.mesAtual));
        $('#tabela-despesas').html('<tr><td colspan="6" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>');
        
        const categoriaId = $('#filtro-categoria').val();
        const contaFixa = $('#filtro-conta-fixa').val();

        $.get('ajax.php?acao=despesas-listar', { 
            mes: this.mesAtual,
            categoria_id: categoriaId,
            conta_fixa: contaFixa
        }, function(res) {
            $('#resumo-total-saidas').text(res.resumo.total_saidas_formatado);
            $('#resumo-custo-vida').text(res.resumo.custo_vida_formatado);
            
            despesasJS.dadosOriginais = res.dados;
            despesasJS.renderizarTabela();
        });
    },

    filtrar: function(status) {
        this.filtroAtual = status;
        
        // Atualiza UI dos bots
        $('#filtro-todas, #filtro-pendentes, #filtro-pagas').removeClass('bg-gray-800 text-white dark:bg-white dark:text-gray-900').addClass('bg-gray-200 text-gray-600 dark:bg-darkborder dark:text-gray-400');
        $(`#filtro-${status}`).removeClass('bg-gray-200 text-gray-600 dark:bg-darkborder dark:text-gray-400').addClass('bg-gray-800 text-white dark:bg-white dark:text-gray-900');
        
        this.renderizarTabela();
    },

    renderizarTabela: function() {
        let dadosFiltrados = this.dadosOriginais;
        
        if (this.filtroAtual === 'pendentes') {
            dadosFiltrados = this.dadosOriginais.filter(d => d.qtd_parcelas_pagas < d.qtd_total_parcelas);
        } else if (this.filtroAtual === 'pagas') {
            dadosFiltrados = this.dadosOriginais.filter(d => d.qtd_parcelas_pagas == d.qtd_total_parcelas);
        }

        let html = '';
        if(dadosFiltrados.length === 0) {
            html = `<tr><td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">Nenhuma despesa ${this.filtroAtual === 'todas' ? 'encontrada' : this.filtroAtual} para este mês.</td></tr>`;
        } else {
            dadosFiltrados.forEach(d => {
                const isTotalPago = parseInt(d.qtd_parcelas_pagas) === parseInt(d.qtd_total_parcelas);
                const statusColorBase = isTotalPago ? 'text-emerald-500' : 'text-gray-400 hover:text-emerald-500';
                const iconStatus = isTotalPago ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle';
                
                const statusBadge = isTotalPago 
                    ? '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 rounded text-xs font-bold whitespace-nowrap">Pago</span>'
                    : `<span class="px-2 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 rounded text-xs font-bold whitespace-nowrap">Pendente (${d.qtd_parcelas_pagas}/${d.qtd_total_parcelas})</span>`;

                html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group ${isTotalPago ? 'opacity-70' : ''}">
                    <!-- Checkbox de Pagamento Rápido -->
                    <td class="p-4 text-center align-middle">
                        ${!isTotalPago ? `
                        <button onclick="despesasJS.pagarProxima(${d.lancamento_id})" class="${statusColorBase} transition-colors text-xl" title="Pagar Próxima Parcela">
                            <i class="${iconStatus}"></i>
                        </button>
                        ` : `
                        <i class="${iconStatus} text-emerald-500 text-xl"></i>
                        `}
                    </td>
                    <td class="p-4 text-gray-500 dark:text-gray-400 text-sm whitespace-nowrap">
                        ${despesasJS.formatarDataBR(d.data_vencimento)}
                    </td>
                    <td class="p-4 text-gray-900 dark:text-gray-100 font-medium">
                        <div class="flex items-center gap-2">
                            ${d.descricao}
                            ${d.conta_fixa == 1 ? '<i class="fa-solid fa-shield-cat text-yellow-500 text-xs" title="Conta Fixa Mapeada"></i>' : ''}
                        </div>
                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                            Parcela ${d.numero_parcela}/${d.total_parcelas}
                        </div>
                    </td>
                    <td class="p-4 text-gray-500 dark:text-gray-400 text-sm">
                        <span class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded text-xs">${d.categoria_nome}</span>
                    </td>
                    <td class="p-4 text-center">
                        ${statusBadge}
                    </td>
                    <td class="p-4 text-right text-red-500 font-bold whitespace-nowrap"><span class="valor-sensivel">${despesasJS.formatarMoeda(d.valor)}</span></td>
                    <td class="p-4 text-center">
                        <button onclick="despesasJS.abrirDetalhes(${d.parcela_id})" class="text-gray-500 opacity-0 group-hover:opacity-100 hover:bg-gray-100 dark:hover:bg-white/10 p-2 rounded-lg transition-all" title="Ver Detalhes"><i class="fa-solid fa-search"></i></button>
                        <button onclick="despesasJS.editar(${d.parcela_id})" class="text-blue-500 opacity-0 group-hover:opacity-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded-lg transition-all ml-1" title="Editar Parcela"><i class="fa-solid fa-pen"></i></button>
                    </td>
                </tr>`;
            });
        }
        $('#tabela-despesas').html(html);
    },

    carregarCategorias: function(callback) {
        $.get('ajax.php?acao=categorias-listar', function(res) {
            if (!res.dados) return;

            let html = '<option value="">Selecione...</option>';
            let htmlFiltro = '<option value="">Todas as Categorias</option>';
            res.dados.forEach(c => {
                html += `<option value="${c.id}">${c.nome}</option>`;
                htmlFiltro += `<option value="${c.id}">${c.nome}</option>`;
            });
            $('#categoria_id, #despesa_categoria').html(html);
            $('#filtro-categoria').html(htmlFiltro);
            if(callback) callback();
        });
    },

    abrirModalCadastro: function() {
        this.carregarCategorias(() => {
            $('#form-despesa')[0].reset();
            $('#lancamento_id').val('');
            $('#despesa_data').val(new Date().toISOString().substring(0, 10)).prop('readonly', false);
            $('#despesa_valor').prop('readonly', false);
            $('#modal-despesa-title').text('Nova Despesa');
            
            $('#bloco-valores').show();
            $('#despesa_valor, #despesa_data').prop('required', true);
            
            $('#is_parcelada').prop('checked', false).prop('disabled', false).closest('label').show();
            $('#bloco-parcelamento').hide();
            $('#despesa_parcelas').val(1).prop('readonly', false);
            $('#despesa_parcela_inicial').val(1).prop('readonly', false);
            $('#label-vencimento').text('Vencimento Inicial');
            
            this.mostrarModal();
        });
    },

    editar: function(id) {
        const row = this.dadosOriginais.find(d => d.parcela_id == id);
        this.carregarCategorias(() => {
            $.get('ajax.php?acao=despesas-buscar', {lancamento_id: row.lancamento_id}, function(res) {
                const l = res.lancamento;
                const parcelas = res.parcelas || [];
                
                $('#lancamento_id').val(l.id);
                $('#despesa_descricao').val(l.descricao);
                $('#categoria_id').val(l.categoria_id);
                $('#despesa_conta_fixa').prop('checked', l.conta_fixa == 1);

                // Define o status baseado na parcela clicada (id)
                const parcelaAtiva = parcelas.find(p => p.id == id);
                if (parcelaAtiva) {
                    $('#despesa_status').val(parcelaAtiva.data_pagamento ? 'pago' : 'pendente');
                }
                
                // Em Edição, exibir o bloco dos Valores populado mas 100% blindado contra edição local
                $('#bloco-valores').show();
                let valorParaExibir = '';
                if (parcelas.length > 0 && parcelas[0].valor) {
                    valorParaExibir = parseFloat(parcelas[0].valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
                $('#despesa_valor').val(valorParaExibir).prop('readonly', true).prop('required', false);
                $('#despesa_data').val(parcelas.length > 0 ? parcelas[0].data_vencimento.split(' ')[0] : '').prop('readonly', true).prop('required', false);
                
                if (parcelas.length > 1) {
                    $('#is_parcelada').prop('checked', true).prop('disabled', true).closest('label').show();
                    $('#bloco-parcelamento').show();
                    // O valor total_parcelas pode ser inferido pelo tamanho da estrutura
                    $('#despesa_parcelas').val(parcelas[0].total_parcelas).prop('readonly', true);
                    $('#despesa_parcela_inicial').val(parcelas[0].numero_parcela).prop('readonly', true);
                } else {
                    $('#is_parcelada').prop('checked', false).prop('disabled', true).closest('label').show();
                    $('#bloco-parcelamento').hide();
                }
                
                $('#modal-despesa-title').text('Editar Lançamento Principal');
                despesasJS.mostrarModal();
            });
        });
    },

    abrirDetalhes: function(id) {
        const row = this.dadosOriginais.find(d => d.parcela_id == id);
        if(!row) return;

        $.get('ajax.php?acao=despesas-buscar', {lancamento_id: row.lancamento_id}, function(res) {
            const l = res.lancamento;
            const parcelas = res.parcelas;

            $('#det-descricao').text(l.descricao);
            $('#det-categoria').text(l.categoria_nome);
            $('#det-fixa-badge').toggleClass('hidden flex', l.conta_fixa == 1).toggleClass('hidden', l.conta_fixa != 1);

            let htmlTbody = '';
            parcelas.forEach(p => {
                const isPaga = p.data_pagamento !== null;
                const statusBadge = isPaga 
                    ? '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 rounded text-xs font-bold">Pago</span>'
                    : '<span class="px-2 py-1 bg-gray-100 text-gray-700 dark:bg-darkborder dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded text-xs font-bold">Pendente</span>';
                    
                const btnPayHtml = `<button onclick="despesasJS.pagarParcelaDetalhe(${p.id}, ${l.id}, ${isPaga})" class="${isPaga ? 'text-emerald-500' : 'text-gray-400 hover:text-emerald-500'} transition-colors mt-0.5 text-lg" title="${isPaga ? 'Estornar Pagamento' : 'Registrar Pagamento'}"><i class="${isPaga ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'}"></i></button>`;
                
                const btnEditHtml = `<button onclick="despesasJS.editarParcelaDetalhe(${p.id}, ${l.id}, '${p.valor}', '${p.data_vencimento}')" class="text-blue-500 hover:text-blue-700 transition-colors mt-1" title="Editar Parcela / Amortizar Original"><i class="fa-solid fa-pen-to-square"></i></button>`;

                const detalheDesconto = p.desconto > 0 ? `<br><span class="text-xs text-green-500">(- <span class="valor-sensivel">${despesasJS.formatarMoeda(p.desconto)}</span>)</span>` : '';
                const valorFinal = parseFloat(p.valor) - parseFloat(p.desconto);

                htmlTbody += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-4 text-gray-900 dark:text-gray-100 font-medium">${p.numero_parcela} / ${p.total_parcelas}</td>
                        <td class="p-4 text-gray-500 dark:text-gray-400 text-sm whitespace-nowrap">${despesasJS.formatarDataBR(p.data_vencimento)} <br> <span class="text-xs ${isPaga ? 'text-emerald-600' : 'hidden'}">Pago: ${p.data_pagamento ? despesasJS.formatarDataBR(p.data_pagamento) : ''}</span></td>
                        <td class="p-4 text-right text-red-500 font-bold whitespace-nowrap"><span class="valor-sensivel">${despesasJS.formatarMoeda(valorFinal)}</span> ${detalheDesconto}</td>
                        <td class="p-4 text-center">${statusBadge}</td>
                        <td class="p-4 text-center flex items-center justify-center gap-3">${btnPayHtml} ${btnEditHtml}</td>
                    </tr>
                `;
            });
            $('#det-parcelas-tbody').html(htmlTbody);

            // Action delete button config
            const btnExcluirHtml = `<button type="button" onclick="despesasJS.excluirLancamentoTotal(${row.lancamento_id})" class="mr-auto px-4 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors font-medium flex items-center gap-2 text-sm">
                <i class="fa-solid fa-trash"></i> Excluir Lançamento Completo
            </button>`;
            
            if($('#btn-excluir-detalhes').length === 0) {
                $('#det-actions-container').prepend(`<div id="btn-excluir-detalhes" class="flex-1">${btnExcluirHtml}</div>`);
            } else {
                $('#btn-excluir-detalhes').html(btnExcluirHtml);
            }

            $('#modal-detalhes-backdrop').removeClass('hidden');
            $('#modal-detalhes').removeClass('hidden').addClass('flex');
            setTimeout(() => {
                $('#modal-detalhes-content').removeClass('scale-95 opacity-0');
            }, 10);
        });
    },

    pagarParcelaDireto: function(parcelaId, lancamentoId) {
        // Redireciona para o detalhe para usar a mesma modal logic
        this.abrirDetalhes(parcelaId);
    },

    pagarProxima: function(lancamentoId) {
        $.get('ajax.php?acao=contas-listar', function(resContas) {
            let optionsContas = '<option value="">Selecione a Conta...</option>';
            resContas.dados.forEach(c => {
                optionsContas += `<option value="${c.id}">${c.nome}</option>`;
            });

            const dataHoje = new Date().toISOString().substring(0, 10);

            Swal.fire({
                title: 'Pagar Próxima Parcela',
                html: `
                    <div class="text-left mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data do Pagto</label>
                        <input type="date" id="swal-pag-data" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 mb-4" value="${dataHoje}">
                        
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carteira de Pagamento</label>
                        <select id="swal-pag-conta" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 mb-4">
                            ${optionsContas}
                        </select>

                        <label class="block text-sm font-medium text-gray-700 mb-1">Desconto Recebido</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500">R$</span></div>
                            <input type="text" id="swal-pag-desc" class="moeda_brl pl-10 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900" value="0,00">
                        </div>
                    </div>
                `,
                didOpen: () => {
                    $('.moeda_brl').mask('#.##0,00', {reverse: true});
                },
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Confirmar Pagamento',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const conta = document.getElementById('swal-pag-conta').value;
                    if(!conta) {
                        Swal.showValidationMessage('Selecione uma carteira!');
                        return false;
                    }
                    return {
                        data_pagamento: document.getElementById('swal-pag-data').value,
                        desconto: document.getElementById('swal-pag-desc').value,
                        conta_pagamento_id: conta
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('ajax.php?acao=despesas-pagar_proxima', {
                        lancamento_id: lancamentoId,
                        data_pagamento: result.value.data_pagamento,
                        desconto: result.value.desconto,
                        conta_pagamento_id: result.value.conta_pagamento_id
                    }, function(res) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.mensagem,
                            showConfirmButton: false,
                            timer: 3000
                        });
                        despesasJS.carregar();
                    }, 'json');
                }
            });
        });
    },
    
    pagarParcelaDetalhe: function(parcelaId, lancamentoId, isPaga) {
        if (isPaga) {
            Swal.fire({
                title: 'Estornar Pagamento?',
                text: "Isoladamente o desconto e data de baixa desta parcela serão desfeitos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sim, estornar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('ajax.php?acao=parcelas-pagar', { id: parcelaId, estorno: 1 }, function(res) {
                        despesasJS.abrirDetalhes(despesasJS.dadosOriginais.find(d => d.lancamento_id == lancamentoId)?.parcela_id || parcelaId);
                        despesasJS.carregar();
                    }, 'json');
                }
            });
            return;
        }

        const dataHoje = new Date().toISOString().substring(0, 10);

        $.get('ajax.php?acao=contas-listar', function(resContas) {
            let optionsContas = '<option value="">Selecione a Conta...</option>';
            resContas.dados.forEach(c => {
                optionsContas += `<option value="${c.id}">${c.nome}</option>`;
            });

            Swal.fire({
                title: 'Registrar Pagamento',
                html: `
                    <div class="text-left mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data do Pagto</label>
                        <input type="date" id="swal-pag-data" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 mb-4" value="${dataHoje}">
                        
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carteira de Pagamento</label>
                        <select id="swal-pag-conta" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 mb-4">
                            ${optionsContas}
                        </select>

                        <label class="block text-sm font-medium text-gray-700 mb-1">Desconto Recebido (Amortização)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500">R$</span></div>
                            <input type="text" id="swal-pag-desc" class="moeda_brl pl-10 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900" value="0,00">
                        </div>
                    </div>
                `,
                didOpen: () => {
                    $('.moeda_brl').mask('#.##0,00', {reverse: true});
                },
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Confirmar Pagamento',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const conta = document.getElementById('swal-pag-conta').value;
                    if(!conta) {
                        Swal.showValidationMessage('Selecione uma carteira de pagamento!');
                        return false;
                    }
                    return {
                        data_pagamento: document.getElementById('swal-pag-data').value,
                        desconto: document.getElementById('swal-pag-desc').value,
                        conta_pagamento_id: conta
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('ajax.php?acao=parcelas-pagar', {
                        id: parcelaId,
                        data_pagamento: result.value.data_pagamento,
                        desconto: result.value.desconto,
                        conta_pagamento_id: result.value.conta_pagamento_id
                    }, function(res) {
                        despesasJS.abrirDetalhes(despesasJS.dadosOriginais.find(d => d.lancamento_id == lancamentoId)?.parcela_id || parcelaId);
                        despesasJS.carregar();
                    }, 'json');
                }
            });
        });
    },

    editarParcelaDetalhe: function(parcelaId, lancamentoId, valor_atual, data_atual) {
        const valorParsed = parseFloat(valor_atual).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        Swal.fire({
            title: 'Gestão da Parcela',
            html: `
                <div class="text-left mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Novo Valor Original</label>
                    <input type="text" id="swal-edit-valor" class="moeda_brl w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 mb-4" value="${valorParsed}">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nova Data de Vencimento</label>
                    <input type="date" id="swal-edit-data" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900" value="${data_atual}">
                </div>
            `,
            didOpen: () => {
                $('.moeda_brl').mask('#.##0,00', {reverse: true});
            },
            showCancelButton: true,
            confirmButtonColor: '#0ea5e9',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Salvar Alteração',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return {
                    valor: document.getElementById('swal-edit-valor').value,
                    data_vencimento: document.getElementById('swal-edit-data').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax.php?acao=parcelas-salvar_individual', {
                    id: parcelaId,
                    valor: result.value.valor,
                    data_vencimento: result.value.data_vencimento
                }, function(res) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.mensagem,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    
                    despesasJS.abrirDetalhes(despesasJS.dadosOriginais.find(d => d.lancamento_id == lancamentoId)?.parcela_id || parcelaId);
                    despesasJS.carregar();
                }, 'json').fail(function() {
                    Swal.fire('Erro', 'Ocorreu um erro ao salvar a amortização.', 'error');
                });
            }
        });
    },

    excluirLancamentoTotal: function(lancamentoId) {
        Swal.fire({
            title: 'Atenção redobrada',
            text: "Excluir esta despesa removerá TAMBÉM todas as suas parcelas registradas no futuro ou no passado. Deseja continuar?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, desejo remover!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax.php?acao=despesas-excluir', {lancamento_id: lancamentoId}, function(res) {
                    Swal.fire('Excluídas!', res.mensagem, 'success');
                    despesasJS.fecharModalDetalhes();
                    despesasJS.carregar();
                });
            }
        });
    },

    mostrarModal: function() {
        $('#modal-despesa-backdrop').removeClass('hidden');
        $('#modal-despesa').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modal-despesa-content').removeClass('scale-95 opacity-0');
        }, 10);
    },
    fecharModal: function() {
        $('#modal-despesa-content').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#modal-despesa-backdrop').addClass('hidden');
            $('#modal-despesa').addClass('hidden').removeClass('flex');
        }, 300);
    },
    fecharModalDetalhes: function() {
        $('#modal-detalhes-content').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#modal-detalhes-backdrop').addClass('hidden');
            $('#modal-detalhes').addClass('hidden').removeClass('flex');
        }, 300);
    }
};

$(document).ready(function() {
    // Only load if table exists
    if($('#tabela-despesas').length) {
        despesasJS.carregarCategorias(() => {
            despesasJS.carregar();
        });
    }
    
    $('#is_parcelada').on('change', function() {
        if ($(this).is(':checked')) {
            $('#bloco-parcelamento').slideDown(200);
            if(parseInt($('#despesa_parcelas').val()) === 1) {
                $('#despesa_parcelas').val(2); // Provide friendly default if empty
            }
        } else {
            $('#bloco-parcelamento').slideUp(200);
            $('#despesa_parcelas').val(1);
            $('#despesa_parcela_inicial').val(1);
        }
    });
    
    $('#form-despesa').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-despesa');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Salvando...');

        $.post('ajax.php?acao=despesas-salvar', $(this).serialize(), function(res) {
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
            despesasJS.fecharModal();
            despesasJS.carregar();
        }).always(function() {
            btn.prop('disabled', false).html(originalHtml);
        });
    });
});
