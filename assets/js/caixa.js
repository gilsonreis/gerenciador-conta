const caixaJS = {
    mesAtual: new Date().toISOString().substring(0, 7), // YYYY-MM
    
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
        $('#tabela-caixa').html('<tr><td colspan="4" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>');
        
        $.get('ajax.php?acao=caixa-listar', { mes: this.mesAtual }, function(res) {
            $('#resumo-total-entradas').text(res.resumo.total_entradas_formatado);

            let html = '';
            if(res.dados.length === 0) {
                html = '<tr><td colspan="4" class="p-8 text-center text-gray-500 dark:text-gray-400">Nenhuma entrada registrada neste mês.</td></tr>';
            } else {
                caixaJS.dadosOriginais = res.dados;
                res.dados.forEach(e => {
                    const descInfo = e.descricao ? `<div class="text-sm font-medium text-gray-900 dark:text-gray-100">${e.descricao}</div>` : '';
                    const walletBadge = `<span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-2 py-0.5 rounded text-xs font-bold ${e.descricao ? 'mt-1 inline-block' : 'mr-2'}"><i class="fa-solid fa-arrow-trend-up"></i> ${e.conta_nome}</span>`;
                    
                    html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="p-4 text-gray-500 dark:text-gray-400 text-sm whitespace-nowrap">
                            <i class="fa-regular fa-calendar text-gray-400 mr-2"></i> ${caixaJS.formatarDataBR(e.data_entrada)}
                        </td>
                        <td class="p-4">
                            ${descInfo}
                            ${walletBadge}
                        </td>
                        <td class="p-4 text-right text-emerald-600 dark:text-emerald-400 font-bold whitespace-nowrap"><span class="valor-sensivel">${caixaJS.formatarMoeda(e.valor)}</span></td>
                        <td class="p-4 text-center">
                            <button onclick="caixaJS.editar(${e.id})" class="text-blue-500 opacity-0 group-hover:opacity-100 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded-lg transition-all" title="Editar"><i class="fa-solid fa-pen"></i></button>
                            <button onclick="caixaJS.excluir(${e.id})" class="text-red-500 opacity-0 group-hover:opacity-100 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded-lg transition-all ml-1" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tabela-caixa').html(html);
        });
    },
    carregarContas: function(callback) {
        $.get('ajax.php?acao=contas-listar', function(res) {
            let html = '<option value="">Selecione a Conta...</option>';
            res.dados.forEach(c => {
                html += `<option value="${c.id}">${c.nome}</option>`;
            });
            $('#caixa_conta_id').html(html);
            if(callback) callback();
        });
    },

    abrirModalCadastro: function() {
        this.carregarContas(() => {
            $('#form-caixa')[0].reset();
            $('#caixa_id').val('');
            $('#caixa_descricao').val('');
            $('#caixa_data').val(new Date().toISOString().substring(0, 10)); // Default to today
            $('#modal-caixa-title').text('Nova Entrada');
            this.mostrarModal();
        });
    },
    editar: function(id) {
        const row = this.dadosOriginais.find(d => d.id == id);
        this.carregarContas(() => {
            $.get('ajax.php?acao=caixa-buscar', {id: id}, function(res) {
                $('#caixa_id').val(res.dados.id);
                $('#caixa_conta_id').val(res.dados.conta_id);
                $('#caixa_descricao').val(res.dados.descricao || '');
                $('#caixa_valor').val(parseFloat(res.dados.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#caixa_data').val(res.dados.data_entrada);
                $('#modal-caixa-title').text('Editar Entrada');
                caixaJS.mostrarModal();
            });
        });
    },
    excluir: function(id) {
        Swal.fire({
            title: 'Excluir registro?',
            text: "Esta entrada financeira será removida.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, excluir!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('ajax.php?acao=caixa-excluir', {id: id}, function(res) {
                    Swal.fire('Excluído!', res.mensagem, 'success');
                    caixaJS.carregar();
                });
            }
        });
    },
    mostrarModal: function() {
        $('#modal-caixa-backdrop').removeClass('hidden');
        $('#modal-caixa').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modal-caixa-content').removeClass('scale-95 opacity-0');
        }, 10);
    },
    fecharModal: function() {
        $('#modal-caixa-content').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#modal-caixa-backdrop').addClass('hidden');
            $('#modal-caixa').addClass('hidden').removeClass('flex');
        }, 300);
    }
};

$(document).ready(function() {
    caixaJS.carregar();
    
    $('#form-caixa').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-caixa');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i> Salvando...');

        $.post('ajax.php?acao=caixa-salvar', $(this).serialize(), function(res) {
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
            caixaJS.fecharModal();
            caixaJS.carregar();
        }).always(function() {
            btn.prop('disabled', false).html(originalHtml);
        });
    });
});
