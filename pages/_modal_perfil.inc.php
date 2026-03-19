<!-- Profile Modal Backdrop -->
<div id="modal-perfil-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>

<!-- Profile Modal Panel -->
<div id="modal-perfil" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 border border-gray-100 dark:border-darkborder">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/30 text-primary flex items-center justify-center font-bold text-sm" id="perfil-avatar-inicial">
                    ?
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Meu Perfil</h3>
                    <p class="text-xs text-gray-400" id="perfil-role-badge"></p>
                </div>
            </div>
            <button type="button" onclick="perfilJS.fechar()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="form-perfil" class="p-6 space-y-4">

            <!-- Email (somente leitura) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    E-mail <span class="text-xs text-gray-400 font-normal">(não editável)</span>
                </label>
                <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-darkborder rounded-lg">
                    <i class="fa-regular fa-envelope text-gray-400 text-sm"></i>
                    <span id="perfil-email" class="text-sm text-gray-500 dark:text-gray-400 select-all"></span>
                </div>
            </div>

            <!-- Nome -->
            <div>
                <label for="perfil_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-regular fa-user text-gray-400"></i>
                    </div>
                    <input type="text" id="perfil_nome" name="nome" required
                        class="pl-10 w-full px-4 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                        placeholder="Seu nome completo">
                </div>
            </div>

            <!-- Nova Senha -->
            <div>
                <label for="perfil_senha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nova Senha <span class="text-xs text-gray-400 font-normal">(deixe em branco para manter a atual)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="perfil_senha" name="senha"
                        class="pl-10 w-full px-4 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-gray-900 dark:text-white transition-colors"
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Alertas por E-mail -->
            <label class="flex items-center gap-3 cursor-pointer group p-3 border border-gray-200 dark:border-darkborder rounded-lg hover:border-primary/50 transition-colors">
                <input type="checkbox" id="perfil_recebe_alertas" name="recebe_alertas" value="1"
                    class="w-5 h-5 text-primary bg-gray-100 dark:bg-[#121212] border-gray-300 dark:border-darkborder rounded focus:ring-primary focus:ring-2 cursor-pointer">
                <div>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Receber alertas por e-mail</span>
                    <p class="text-xs text-gray-400 mt-0.5">Resumos financeiros e lembretes de vencimentos</p>
                </div>
            </label>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="perfilJS.fechar()"
                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors font-medium border border-transparent dark:hover:border-darkborder">
                    Cancelar
                </button>
                <button type="submit" id="btn-salvar-perfil"
                    class="px-6 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
