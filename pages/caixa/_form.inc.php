<!-- Modal Backdrop -->
<div id="modal-caixa-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>

<!-- Modal Panel -->
<div id="modal-caixa" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-caixa-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="modal-caixa-title">Nova Entrada</h3>
            <button type="button" onclick="caixaJS.fecharModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="form-caixa" class="p-6">
            <input type="hidden" id="caixa_id" name="id">
            
            <div class="mb-4">
                <label for="caixa_conta_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Carteira de Destino</label>
                <select id="caixa_conta_id" name="conta_id" required 
                    class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary text-gray-900 dark:text-white transition-colors">
                    <option value="">Selecione a Conta...</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="caixa_descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição / Origem <span class="text-xs text-gray-400 font-normal">(Opcional)</span></label>
                <input type="text" id="caixa_descricao" name="descricao" 
                    class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary text-gray-900 dark:text-white transition-colors"
                    placeholder="Ex: Salário Mês, Freela Site, etc">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="caixa_valor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">R$</span>
                        </div>
                        <input type="text" id="caixa_valor" name="valor" required 
                            class="moeda_brl pl-10 w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                            placeholder="0,00">
                    </div>
                </div>
                
                <div>
                    <label for="caixa_data" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data da Entrada</label>
                    <input type="date" id="caixa_data" name="data_entrada" required 
                        class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary text-gray-900 dark:text-white transition-colors [color-scheme:light] dark:[color-scheme:dark]">
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="caixaJS.fecharModal()" class="px-4 py-2 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors font-medium border border-transparent dark:hover:border-darkborder">
                    Cancelar
                </button>
                <button type="submit" id="btn-salvar-caixa" class="px-6 py-2 bg-secondary hover:bg-emerald-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
