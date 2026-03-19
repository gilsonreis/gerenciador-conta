<?php
session_start();

// Authentication middleware for views
$pagina = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'home-index';

// Se for a ação de logout (simples, quebra a sessão e redireciona)
if ($pagina === 'auth-logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Redirect to login.php if session not set
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Security: simple routing resolution (dir-file)
$conteudo_view = __DIR__ . "/pages/erros/404.php";

if (strpos($pagina, '-') !== false) {
    list($diretorio, $arquivo) = explode('-', $pagina, 2);
    
    // Basic sanitization against path traversal
    $diretorio = preg_replace('/[^a-zA-Z0-9_]/', '', $diretorio);
    $arquivo = preg_replace('/[^a-zA-Z0-9_]/', '', $arquivo);
    
    $fullFilePath = __DIR__ . "/pages/{$diretorio}/{$arquivo}.inc.php";

    if (file_exists($fullFilePath)) {
        $conteudo_view = $fullFilePath;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Familiar</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        darkbg: '#121212',
                        darkcard: '#1e1e1e',
                        darkborder: '#2a2a2a',
                        primary: '#3b82f6',
                        secondary: '#10b981',
                        danger: '#ef4444'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Modo Privacidade (Valores Sensíveis) */
        body.modo-privacidade .valor-sensivel {
            filter: blur(6px);
            opacity: 0.6;
            user-select: none;
            transition: filter 0.3s ease, opacity 0.3s ease;
        }
    </style>
    <!-- Font Awesome API para icones (Free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>
<body class="bg-gray-100 dark:bg-darkbg text-gray-900 dark:text-gray-100 font-sans antialiased flex h-screen overflow-hidden">
    
    <?php if (isset($_SESSION['usuario_id'])): ?>
    <!-- Sidebar -->
    <aside class="w-64 bg-white dark:bg-darkcard border-r border-gray-200 dark:border-darkborder hidden md:flex flex-col">
        <div class="p-6 border-b border-gray-200 dark:border-darkborder mb-4">
            <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"><i class="fa-solid fa-wallet mr-2"></i>Controle Familiar</h1>
            <div class="flex justify-between items-center mt-2">
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate"><i class="fa-solid fa-user-circle mr-1"></i><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></p>
                <button type="button" class="btn-toggle-privacidade text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors" title="Alternar Modo Privacidade">
                    <i class="fa-regular fa-eye text-lg"></i>
                </button>
            </div>
        </div>
        <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
            <a href="?pagina=home-index" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= str_starts_with($pagina, 'home') ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-house w-5 text-center"></i> Dashboard</a>
            
            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lançamentos</p>
            <a href="?pagina=caixa-listar" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= str_starts_with($pagina, 'caixa') ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-arrow-trend-up text-emerald-500 w-5 text-center"></i> Entradas</a>
            <a href="?pagina=despesas-listar" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= str_starts_with($pagina, 'despesas') ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-arrow-trend-down text-red-500 w-5 text-center"></i> Despesas</a>
            
            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Configurações</p>
            <a href="?pagina=instituicoes-listar" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= str_starts_with($pagina, 'instituicoes') ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-building w-5 text-center"></i> Instituições</a>
            <a href="?pagina=contas-listar" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= $pagina === 'contas-listar' ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-building-columns w-5 text-center"></i> Contas Bancárias</a>
            <a href="?pagina=contas-transferencias" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= $pagina === 'contas-transferencias' ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-money-bill-transfer w-5 text-center"></i> Transferências</a>
            <a href="?pagina=categorias-listar" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= str_starts_with($pagina, 'categorias') ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-tags w-5 text-center"></i> Categorias</a>
            <a href="?pagina=usuarios-listar" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= str_starts_with($pagina, 'usuarios') ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-users w-5 text-center"></i> Usuários</a>
            
            <p class="px-4 pt-4 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Análise</p>
            <a href="?pagina=relatorios-index" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= str_starts_with($pagina, 'relatorios') ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-chart-pie w-5 text-center"></i> Relatórios</a>
            <a href="?pagina=snapshots-listar" class="flex items-center gap-3 py-3 px-4 rounded-lg transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5 <?= str_starts_with($pagina, 'snapshots') ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' ?>"><i class="fa-solid fa-camera-retro w-5 text-center"></i> Saldos de Abertura</a>
        </nav>
        <div class="p-4 mt-auto">
            <a href="?pagina=auth-logout" class="flex justify-center items-center gap-2 w-full py-2.5 px-4 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden <?= !isset($_SESSION['usuario_id']) ? 'w-full' : '' ?>">
        <?php if (isset($_SESSION['usuario_id'])): ?>
        <!-- Topbar Mobile -->
        <header class="md:hidden bg-white dark:bg-darkcard border-b border-gray-200 dark:border-darkborder p-4 flex justify-between items-center shadow-sm z-10">
            <h1 class="text-lg font-bold bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"><i class="fa-solid fa-wallet mr-2"></i>Controle Familiar</h1>
            <div class="flex gap-2">
                <button type="button" class="btn-toggle-privacidade text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white p-2" title="Alternar Modo Privacidade">
                    <i class="fa-regular fa-eye text-xl"></i>
                </button>
                <button id="mobile-menu-btn" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white p-2">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </header>

        <!-- Menu Mobile Overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>
        <div id="mobile-menu-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-darkcard z-50 transform -translate-x-full transition-transform duration-300 md:hidden flex flex-col border-r shadow-2xl dark:border-darkborder">
            <div class="p-4 border-b border-gray-200 dark:border-darkborder flex justify-between items-center">
                <p class="font-bold text-gray-800 dark:text-white truncate">Menu</p>
                <button id="mobile-menu-close" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <a href="?pagina=home-index" class="block py-3 px-4 rounded-lg <?= str_starts_with($pagina, 'home') ? 'bg-primary/20 text-blue-400' : 'text-gray-300' ?>">Dashboard</a>
                <a href="?pagina=caixa-listar" class="block py-3 px-4 rounded-lg <?= str_starts_with($pagina, 'caixa') ? 'bg-primary/20 text-blue-400' : 'text-gray-300' ?>">Entradas (Caixa)</a>
                <a href="?pagina=despesas-listar" class="block py-3 px-4 rounded-lg <?= str_starts_with($pagina, 'despesas') ? 'bg-primary/20 text-blue-400' : 'text-gray-300' ?>">Despesas</a>
                <a href="?pagina=instituicoes-listar" class="block py-3 px-4 rounded-lg <?= str_starts_with($pagina, 'instituicoes') ? 'bg-primary/20 text-blue-400' : 'text-gray-300' ?>">Instituições</a>
                <a href="?pagina=contas-listar" class="block py-3 px-4 rounded-lg <?= str_starts_with($pagina, 'contas') ? 'bg-primary/20 text-blue-400' : 'text-gray-300' ?>">Contas Bancárias</a>
                <a href="?pagina=categorias-listar" class="block py-3 px-4 rounded-lg <?= str_starts_with($pagina, 'categorias') ? 'bg-primary/20 text-blue-400' : 'text-gray-300' ?>">Categorias</a>
                <a href="?pagina=usuarios-listar" class="block py-3 px-4 rounded-lg <?= str_starts_with($pagina, 'usuarios') ? 'bg-primary/20 text-blue-400' : 'text-gray-300' ?>">Usuários</a>
                <a href="?pagina=relatorios-index" class="block py-3 px-4 rounded-lg <?= str_starts_with($pagina, 'relatorios') ? 'bg-primary/20 text-blue-400' : 'text-gray-300' ?>">Relatórios</a>
            </nav>
        </div>
        <?php endif; ?>
        
    <!-- View Content Area -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-darkbg p-4 md:p-8">
            <div id="app-content" class="max-w-7xl mx-auto h-full w-full">
                <?php include $conteudo_view; ?>
            </div>
        </div>
    </main>

    <!-- SweetAlert2 para alertas bonitos -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Scripts da Aplicação -->
    <script src="assets/js/app.js?v=<?= time() ?>"></script>
    <?php if ($pagina === 'auth-login'): ?>
        <script src="assets/js/auth.js?v=<?= time() ?>"></script>
    <?php else: ?>
        <?php 
        if (isset($diretorio) && file_exists(__DIR__ . "/assets/js/{$diretorio}.js")) {
            echo "<script src=\"assets/js/{$diretorio}.js?v=" . time() . "\"></script>";
        }
        ?>
    <?php endif; ?>
</body>
</html>
