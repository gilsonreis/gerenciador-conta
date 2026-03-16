<!-- Modal Backdrop -->
<div id="modal-instituicao-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>

<!-- Modal Panel -->
<div id="modal-instituicao" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-instituicao-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="modal-instituicao-title">Nova Instituição</h3>
            <button type="button" onclick="instituicoesJS.fecharModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="form-instituicao" class="p-6">
            <input type="hidden" id="instituicao_id" name="id">
            
            <div class="mb-4">
                <label for="instituicao_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Instituição</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <input type="text" id="instituicao_nome" name="nome" required 
                        class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                        placeholder="Ex: Família Silva">
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="instituicoesJS.fecharModal()" class="px-4 py-2 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors font-medium border border-transparent dark:hover:border-darkborder">
                    Cancelar
                </button>
                <button type="submit" id="btn-salvar-instituicao" class="px-6 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
