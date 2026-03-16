<!-- Modal Detalhes Backdrop -->
<div id="modal-detalhes-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>

<!-- Modal Detalhes Panel -->
<div id="modal-detalhes" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-detalhes-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder">
        
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
        <div class="p-6">
            <div class="flex items-center justify-center mb-6">
                <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center text-red-500 text-2xl">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Descrição</p>
                    <p class="text-gray-900 dark:text-white font-medium" id="det-descricao">-</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Categoria</p>
                        <p class="text-gray-900 dark:text-white" id="det-categoria">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Valor</p>
                        <p class="text-primary font-bold" id="det-valor">R$ 0,00</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Vencimento</p>
                        <p class="text-gray-900 dark:text-white" id="det-vencimento">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Parcela</p>
                        <p class="text-gray-900 dark:text-white" id="det-parcela">-</p>
                    </div>
                </div>

                <div class="pt-4 mt-2 border-t border-gray-100 dark:border-darkborder flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Status</p>
                        <div id="det-status" class="mt-1">-</div>
                    </div>
                    
                    <div id="det-fixa-badge" class="hidden items-center gap-1 px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-500 text-xs rounded font-medium">
                        <i class="fa-solid fa-shield-cat"></i> Fixo
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end">
                <button type="button" onclick="despesasJS.fecharModalDetalhes()" class="w-full py-2 bg-gray-100 dark:bg-darkborder hover:bg-gray-200 dark:hover:bg-gray-800 text-gray-800 dark:text-white rounded-lg transition-colors font-medium">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>
