<!-- Modal Backdrop -->
<div id="modal-usuario-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>

<!-- Modal Panel -->
<div id="modal-usuario" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-usuario-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="modal-usuario-title">Novo Usuário</h3>
            <button type="button" onclick="usuariosJS.fecharModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="form-usuario" class="p-6">
            <input type="hidden" id="usuario_id" name="id">
            
            <div class="mb-4">
                <label for="usuario_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Completo</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-regular fa-user text-gray-400"></i>
                    </div>
                    <input type="text" id="usuario_nome" name="nome" required 
                        class="pl-10 w-full px-4 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                        placeholder="João da Silva">
                </div>
            </div>

            <?php if (($_SESSION['usuario_role'] ?? '') === 'super_admin'): ?>
            <div class="mb-4">
                <label for="usuario_instituicao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instituição/Família</label>
                <select id="usuario_instituicao" name="instituicao_id" required
                    class="w-full px-4 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors">
                    <option value="">Carregando...</option>
                </select>
            </div>
            <?php else: ?>
            <!-- Admin/Manager: instituição fixada pela sessão, não pode ser trocada -->
            <input type="hidden" name="instituicao_id" value="<?= (int)$_SESSION['instituicao_id'] ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instituição/Família</label>
                <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-darkborder rounded-lg">
                    <i class="fa-solid fa-building text-gray-400 text-sm"></i>
                    <span class="text-sm text-gray-500 dark:text-gray-400 italic">Fixada pela sua conta (somente super_admin pode alterar)</span>
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="usuario_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-regular fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" id="usuario_email" name="email" required 
                        class="pl-10 w-full px-4 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                        placeholder="joao@familia.com">
                </div>
            </div>

            <div class="mb-4" id="bloco-alertas" style="display: none;">
                <label class="flex items-center gap-2 cursor-pointer group p-3 border border-gray-200 dark:border-darkborder rounded-lg hover:border-primary/50 dark:hover:border-primary/50 transition-colors">
                    <input type="checkbox" id="usuario_recebe_alertas" name="recebe_alertas" value="1" checked
                        class="w-5 h-5 text-primary bg-gray-100 dark:bg-[#121212] border-gray-300 dark:border-darkborder rounded focus:ring-primary focus:ring-2 transition-colors cursor-pointer">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-primary transition-colors">Deseja receber e-mails de alerta?</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Marque para receber resumos financeiros de vencimentos</span>
                    </div>
                </label>
            </div>

            <div class="mb-4">
                <label for="usuario_senha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha <span class="text-xs text-gray-400 font-normal">(Opcional ao editar)</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="usuario_senha" name="senha" 
                        class="pl-10 w-full px-4 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="usuariosJS.fecharModal()" class="px-4 py-2 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors font-medium border border-transparent dark:hover:border-darkborder">
                    Cancelar
                </button>
                <button type="submit" id="btn-salvar-usuario" class="px-6 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
