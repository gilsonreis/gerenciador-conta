<div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <div class="bg-blue-500/10 p-2 rounded-lg"><i class="fa-solid fa-money-bill-transfer text-blue-500"></i></div> Transferência entre Contas
        </h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Movimente valores entre suas carteiras e contas bancárias.</p>
    </div>
    
    <button onclick="transferenciasJS.abrirModal()" class="w-full md:w-auto bg-primary hover:bg-blue-600 text-white px-5 py-2.5 rounded-xl font-medium transition-all shadow-sm flex items-center justify-center gap-2 group">
        <i class="fa-solid fa-plus group-hover:rotate-90 transition-transform"></i> Nova Transferência
    </button>
</div>

<?php if (($_SESSION['usuario_role'] ?? '') === 'super_admin'): ?>
<div class="flex gap-4 mb-4">
    <div class="w-full sm:w-1/3">
        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
            <i class="fa-solid fa-building mr-1"></i> Instituição
        </label>
        <select id="filtro-instituicao-transferencias" onchange="transferenciasJS.onInstituicaoChange()"
            class="w-full px-3 py-2 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-sm focus:ring-2 focus:ring-primary text-gray-900 dark:text-gray-100">
            <option value="">Todas as Instituições</option>
        </select>
    </div>
</div>
<?php endif; ?>

<!-- Tabela de Histórico -->
<div class="bg-white dark:bg-darkcard rounded-2xl shadow-sm border border-gray-100 dark:border-darkborder overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder bg-gray-50/50 dark:bg-white/5">
        <h3 class="font-bold text-gray-700 dark:text-white text-sm">Histórico Recente</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                    <th class="p-4 font-semibold w-32">Data</th>
                    <th class="p-4 font-semibold">Movimentação</th>
                    <th class="p-4 font-semibold">Descrição</th>
                    <th class="p-4 font-semibold text-right">Valor</th>
                    <?php if (($_SESSION['usuario_role'] ?? '') === 'super_admin'): ?>
                    <th class="p-4 font-semibold">Instituição</th>
                    <?php endif; ?>
                    <th class="p-4 font-semibold w-24 text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-transferencias" class="divide-y divide-gray-100 dark:divide-darkborder">
                <!-- Preenchido via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Transferência -->
<div id="modal-transferencia-backdrop" class="fixed inset-0 bg-black/60 hidden z-40 transition-opacity"></div>
<div id="modal-transferencia" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div id="modal-transferencia-content" class="bg-white dark:bg-darkcard rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-darkborder">
        
        <div class="px-6 py-4 border-b border-gray-100 dark:border-darkborder flex justify-between items-center bg-gray-50 dark:bg-white/5">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Nova Transferência</h3>
            <button type="button" onclick="transferenciasJS.fecharModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>

        <form id="form-transferencia" class="p-6">
            <div class="space-y-4">
                <?php if (($_SESSION['usuario_role'] ?? '') === 'super_admin'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instituição</label>
                    <select name="instituicao_id" id="transferencia_instituicao_id" required class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary"
                        onchange="transferenciasJS.carregarContasDaInstituicao(this.value)">
                        <option value="">Selecione...</option>
                    </select>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Conta de Origem (Saída)</label>
                    <select name="conta_origem_id" id="conta_origem_id" required class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Conta de Destino (Entrada)</label>
                    <select name="conta_destino_id" id="conta_destino_id" required class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor</label>
                        <div class="relative">
                            <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">R$</span>
                            <input type="text" name="valor" id="transferencia_valor" required class="moeda_brl w-full pl-9 pr-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary" placeholder="0,00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data</label>
                        <input type="date" name="data_transferencia" id="transferencia_data" required class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observação (Opcional)</label>
                    <input type="text" name="descricao" id="transferencia_descricao" class="w-full px-4 py-2.5 bg-white dark:bg-[#121212] border border-gray-300 dark:border-darkborder rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary" placeholder="Ex: Pix para poupança">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="transferenciasJS.fecharModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 font-medium font-sm hover:underline">Cancelar</button>
                <button type="submit" id="btn-salvar-transferencia" class="bg-primary hover:bg-blue-600 text-white px-6 py-2 rounded-xl font-bold transition-all shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Realizar Transferência
                </button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/transferencias.js?v=<?= time() ?>"></script>
