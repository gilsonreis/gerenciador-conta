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
        
        $.get('ajax.php?acao=despesas-listar', { mes: this.mesAtual }, function(res) {
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
            dadosFiltrados = this.dadosOriginais.filter(d => d.status === 'pendente');
        } else if (this.filtroAtual === 'pagas') {
            dadosFiltrados = this.dadosOriginais.filter(d => d.status === 'pago');
        }

        let html = '';
        if(dadosFiltrados.length === 0) {
            html = `<tr><td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">Nenhuma despesa ${this.filtroAtual === 'todas' ? 'encontrada' : this.filtroAtual} para este mês.</td></tr>`;
        } else {
            dadosFiltrados.forEach(d => {
                const isPago = d.status === 'pago';
                const statusColorBase = isPago ? 'text-emerald-500' : 'text-gray-400 hover:text-emerald-500';
                const iconStatus = isPago ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle';
                
                html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group ${isPago ? 'opacity-70' : ''}">
                    <!-- Checkbox de Pagamento Rápido -->
                    <td class="p-4 text-center align-middle">
                        <button onclick="despesasJS.alternarStatus(${d.parcela_id})" class="${statusColorBase} transition-colors text-xl" title="Marcar como ${isPago ? 'Pendente' : 'Pago'}">
                            <i class="${iconStatus}"></i>
                        </button>
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
                    <td class="p-4 text-right text-red-500 font-bold whitespace-nowrap">${despesasJS.formatarMoeda(d.valor)}</td>
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
            let html = '<option value="">Selecione...</option>';
            res.dados.forEach(c => {
                html += `<option value="${c.id}">${c.nome}</option>`;
            });
            $('#despesa_categoria').html(html);
            if(callback) callback();
        });
    },

    abrirModalCadastro: function() {
        this.carregarCategorias(() => {
            $('#form-despesa')[0].reset();
            $('#parcela_id').val('');
            $('#despesa_data').val(new Date().toISOString().substring(0, 10));
            $('#modal-despesa-title').text('Nova Despesa');
            
            // Exibir bloco de parcelamento
            $('#bloco-parcelamento').show();
            $('#label-vencimento').text('Vencimento Inicial');
            
            this.mostrarModal();
        });
    },

    editar: function(id) {
        this.carregarCategorias(() => {
            $.get('ajax.php?acao=despesas-buscar', {id: id}, function(res) {
                const d = res.dados;
                $('#parcela_id').val(d.parcela_id);
                $('#despesa_descricao').val(d.descricao);
                $('#despesa_categoria').val(d.categoria_id);
                $('#despesa_conta_fixa').prop('checked', d.conta_fixa == 1);
                $('#despesa_valor').val(parseFloat(d.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#despesa_data').val(d.data_vencimento);
                $('#despesa_status').val(d.status);
                
                // Esconder parcelamento na edição (edita apenas 1 parcela por vez)
                $('#bloco-parcelamento').hide();
                $('#label-vencimento').text('Vencimento desta parcela (' + d.numero_parcela + '/' + d.total_parcelas + ')');
                
                $('#modal-despesa-title').text('Editar Parcela Corrente');
                despesasJS.mostrarModal();
            });
        });
    },

    abrirDetalhes: function(id) {
        // Find row in memory safely
        const row = this.dadosOriginais.find(d => d.parcela_id == id);
        if(!row) return;

        $('#det-descricao').text(row.descricao);
        $('#det-categoria').text(row.categoria_nome);
        $('#det-valor').text(this.formatarMoeda(row.valor));
        $('#det-vencimento').text(this.formatarDataBR(row.data_vencimento));
        $('#det-parcela').text(`${row.numero_parcela} de ${row.total_parcelas}`);
        
        $('#det-fixa-badge').toggleClass('hidden flex', row.conta_fixa == 1).toggleClass('hidden', row.conta_fixa != 1);
        
        const isPago = row.status === 'pago';
        $('#det-status').html(isPago 
            ? '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 rounded text-xs font-bold">Pago</span>' 
            : '<span class="px-2 py-1 bg-gray-100 text-gray-700 dark:bg-darkborder dark:text-gray-400 rounded text-xs font-bold border border-gray-200 dark:border-gray-700">Pendente</span>'
        );

        // Prepara botão de excluir a cadeia inteira no footer do detalhes
        // Replace current action buttons to avoid stacking duplicates
        const btnExcluirHtml = `<button type="button" onclick="despesasJS.excluirLancamentoTotal(${row.lancamento_id})" class="mr-auto px-4 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors font-medium flex items-center gap-2">
            <i class="fa-solid fa-trash"></i> Excluir Cadeia (Todas parcelas)
        </button>`;
        
        if($('#btn-excluir-detalhes').length === 0) {
            $('#modal-detalhes-content .p-6 .mt-8').prepend(`<div id="btn-excluir-detalhes" class="flex-1">${btnExcluirHtml}</div>`);
        } else {
            $('#btn-excluir-detalhes').html(btnExcluirHtml);
        }

        $('#modal-detalhes-backdrop').removeClass('hidden');
        $('#modal-detalhes').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modal-detalhes-content').removeClass('scale-95 opacity-0');
        }, 10);
    },

    alternarStatus: function(id) {
        $.post('ajax.php?acao=despesas-pagar', {id: id}, function() {
            despesasJS.carregar(); // Recarrega para bater valores e ui
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
        despesasJS.carregar();
    }
    
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
