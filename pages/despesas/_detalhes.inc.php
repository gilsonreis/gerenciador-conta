<!-- Modal Detalhes Backdrop -->
<div id="modal-detalhes-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>

<!-- Modal Detalhes Panel -->
<div id="modal-detalhes" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-detalhes-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-4xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary"></i> Detalhes da Despesa
            </h3>
            <button type="button" onclick="despesasJS.fecharModalDetalhes()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="bg-gray-50 dark:bg-[#1a1a1a] p-6 border-b border-gray-100 dark:border-darkborder shrink-0">
            <div class="flex flex-col md:flex-row gap-4 justify-between md:items-center">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center text-red-500 text-2xl shrink-0">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mb-1">Descrição do Lançamento Pai</p>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white" id="det-descricao">-</h4>
                            <div id="det-fixa-badge" class="hidden items-center gap-1 px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500 text-xs rounded font-medium">
                                <i class="fa-solid fa-shield-cat"></i> Fixo
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mb-1">Categoria</p>
                        <p class="text-gray-900 dark:text-white font-medium bg-white dark:bg-darkcard px-3 py-1.5 rounded-lg border border-gray-200 dark:border-darkborder inline-block" id="det-categoria">-</p>
                    </div>
                    <!-- Institutions can be added here if needed -->
                </div>
            </div>
        </div>

        <!-- Content Table -->
        <div class="p-0 overflow-y-auto flex-1 flex flex-col">
            <!-- Search Bar -->
            <div class="p-4 border-b border-gray-100 dark:border-darkborder bg-white dark:bg-darkcard sticky top-0 z-20">
                <div class="relative max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="text" id="det-busca-parcela" 
                        onkeyup="if(event.key === 'Enter') despesasJS.pesquisarParcelas()" 
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-[#121212] border border-gray-200 dark:border-darkborder rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                        placeholder="Número da parcela ou descrição...">
                </div>
            </div>

            <table class="w-full text-left border-collapse">
                <thead class="sticky top-[73px] bg-white dark:bg-darkcard z-10 shadow-sm">
                    <tr class="border-b border-gray-100 dark:border-darkborder text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold bg-gray-50 dark:bg-white/5">
                        <th class="p-4">Parcela</th>
                        <th class="p-4">Vencimento</th>
                        <th class="p-4 text-right">Valor</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="det-parcelas-tbody" class="divide-y divide-gray-100 dark:divide-darkborder">
                    <!-- Preenchido via AJAX -->
                    <tr><td colspan="4" class="p-8 text-center text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Carregando...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Actions -->
        <div class="p-6 border-t border-gray-100 dark:border-darkborder bg-gray-50 dark:bg-[#1a1a1a] shrink-0" id="det-footer">
            <div id="det-paginacao-parcelas" class="mb-4"></div>
            <div class="flex justify-between items-center" id="det-actions-container">
                <button type="button" onclick="despesasJS.fecharModalDetalhes()" class="px-6 py-2 bg-gray-200 dark:bg-darkborder hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-800 dark:text-white rounded-lg transition-colors font-medium ml-auto">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>
