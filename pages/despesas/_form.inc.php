<!-- Modal Backdrop -->
<div id="modal-despesa-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>

<!-- Modal Panel -->
<div id="modal-despesa" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-despesa-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder max-h-[90vh] flex flex-col">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5 shrink-0">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="modal-despesa-title">Nova Despesa</h3>
            <button type="button" onclick="despesasJS.fecharModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="form-despesa" class="p-6 overflow-y-auto">
            <input type="hidden" id="lancamento_id" name="lancamento_id">
            
            <div class="mb-4">
                <label for="despesa_descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição</label>
                <input type="text" id="despesa_descricao" name="descricao" required 
                    class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                    placeholder="Ex: Conta de Luz, Supermercado">
            </div>

            <div class="mb-4">
                <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria</label>
                <div class="flex items-center gap-2">
                    <select id="categoria_id" name="categoria_id" required
                        class="flex-1 px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors">
                        <option value="">Selecione...</option>
                        <!-- Populate via JS -->
                    </select>
                    <button type="button" id="btn-nova-categoria" class="p-2.5 bg-gray-100 dark:bg-white/5 border border-gray-300 dark:border-darkborder rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/10 transition-colors" title="Nova Categoria">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="mb-5">
                <label class="flex items-center gap-2 cursor-pointer group p-3 border border-gray-200 dark:border-darkborder rounded-lg hover:border-primary/50 dark:hover:border-primary/50 transition-colors">
                    <input type="checkbox" id="despesa_conta_fixa" name="conta_fixa" value="1" 
                        class="w-5 h-5 text-primary bg-gray-100 dark:bg-[#121212] border-gray-300 dark:border-darkborder rounded focus:ring-primary focus:ring-2 transition-colors cursor-pointer">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-primary transition-colors">Conta Fixa de Sobrevivência</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Marque se for custo essencial (luz, água, aluguel)</span>
                    </div>
                </label>
            </div>

            <div id="bloco-valores">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="despesa_valor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400">R$</span>
                            </div>
                            <input type="text" id="despesa_valor" name="valor" required 
                                class="moeda_brl pl-10 w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                                placeholder="0,00">
                        </div>
                    </div>
                    
                    <div>
                        <label for="despesa_data" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" id="label-vencimento">Vencimento Inicial</label>
                        <input type="date" id="despesa_data" name="data_vencimento" required 
                            class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                </div>

                <div class="mb-5 border-t border-gray-100 dark:border-darkborder pt-5">
                    <label class="flex items-center gap-2 cursor-pointer mb-4">
                        <input type="checkbox" id="is_parcelada" class="w-5 h-5 text-primary bg-gray-100 dark:bg-[#121212] border-gray-300 dark:border-darkborder rounded focus:ring-primary focus:ring-2 transition-colors cursor-pointer">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Esta é uma conta parcelada?</span>
                    </label>

                    <div id="bloco-parcelamento" style="display: none;" class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-white/5 p-4 rounded-xl border border-gray-100 dark:border-darkborder">
                        <div>
                            <label for="despesa_parcelas" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total de Parcelas</label>
                            <input type="number" id="despesa_parcelas" name="total_parcelas" min="1" max="120" value="1"
                                class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label for="despesa_parcela_inicial" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Iniciar na Parcela</label>
                            <input type="number" id="despesa_parcela_inicial" name="parcela_inicial" min="1" max="120" value="1"
                                class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="despesa_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status do Pagamento</label>
                <select id="despesa_status" name="status" required
                    class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors">
                    <option value="pendente">Pendente / Não Pago</option>
                    <option value="pago">Pago / Quitado</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="despesasJS.fecharModal()" class="px-4 py-2 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors font-medium border border-transparent dark:hover:border-darkborder">
                    Cancelar
                </button>
                <button type="submit" id="btn-salvar-despesa" class="px-6 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nova Categoria (Mini-Modal via AJAX) -->
<div id="modal-nova-categoria-backdrop" class="fixed inset-0 bg-black/40 hidden z-[60] transition-opacity"></div>
<div id="modal-nova-categoria" class="fixed inset-0 hidden z-[70] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-darkcard rounded-xl shadow-2xl w-full max-w-xs transform transition-all border border-gray-100 dark:border-darkborder overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-darkborder bg-gray-50 dark:bg-white/5 font-bold text-gray-800 dark:text-white text-sm">
            Nova Categoria
        </div>
        <div class="p-5">
            <input type="text" id="input-nova-categoria" 
                class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors text-sm"
                placeholder="Ex: Assinaturas, Lazer...">
            
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" id="btn-cancelar-categoria" class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium">
                    Cancelar
                </button>
                <button type="button" id="btn-salvar-categoria" class="px-4 py-1.5 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium text-xs shadow-sm">
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>
