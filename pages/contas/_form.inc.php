<!-- Modal Panel -->
<div id="modal-conta-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>
<div id="modal-conta" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-conta-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-sm transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder max-h-[90vh] flex flex-col">
        
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5 shrink-0">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="modal-conta-title">Nova Conta</h3>
            <button type="button" onclick="contasJS.fecharModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <form id="form-conta" class="p-6 overflow-y-auto">
            <input type="hidden" id="conta_id" name="id">

            <?php if (($_SESSION['usuario_role'] ?? '') === 'super_admin'): ?>
            <div class="mb-4">
                <label for="conta_instituicao_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instituição</label>
                <select id="conta_instituicao_id" name="instituicao_id" required
                    class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors">
                    <option value="">Selecione...</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="conta_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Conta / Carteira</label>
                <input type="text" id="conta_nome" name="nome" required 
                    class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                    placeholder="Ex: Itaú, Bradesco, Dinheiro Físico">
            </div>

            <div class="mb-4">
                <label for="conta_saldo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Saldo Atual</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 dark:text-gray-400">R$</span>
                    </div>
                    <input type="text" id="conta_saldo" name="saldo_inicial" required 
                        class="moeda_brl pl-10 w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                        placeholder="0,00">
                </div>
                <p class="text-xs text-gray-500 mt-2">Dica: Preencha com o dinheiro base existente nesta carteira de modo que ele não impacte os Dashboards nas Entradas. Serve só de lastro para saídas futuras desta Carteira.</p>
            </div>

            <div class="mt-8 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="contasJS.fecharModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors font-medium border border-transparent dark:hover:border-darkborder">
                    Cancelar
                </button>
                <button type="submit" id="btn-salvar-conta" class="px-6 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
