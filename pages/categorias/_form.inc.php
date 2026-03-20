<!-- Modal Backdrop -->
<div id="modal-categoria-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>

<!-- Modal Panel -->
<div id="modal-categoria" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-categoria-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="modal-categoria-title">Nova Categoria</h3>
            <button type="button" onclick="categoriasJS.fecharModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="form-categoria" class="p-6">
            <input type="hidden" id="categoria_id" name="id">

            <?php if (($_SESSION['usuario_role'] ?? '') === 'super_admin'): ?>
            <div class="mb-4">
                <label for="categoria_instituicao_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instituição</label>
                <select id="categoria_instituicao_id" name="instituicao_id" required
                    class="w-full px-4 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors">
                    <option value="">Selecione...</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="categoria_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Categoria</label>
                <input type="text" id="categoria_nome" name="nome" required 
                    class="w-full px-4 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                    placeholder="Ex: Alimentação">
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="categoriasJS.fecharModal()" class="px-4 py-2 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors font-medium border border-transparent dark:hover:border-darkborder">
                    Cancelar
                </button>
                <button type="submit" id="btn-salvar-categoria" class="px-6 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
