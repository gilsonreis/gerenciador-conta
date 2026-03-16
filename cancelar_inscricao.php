<?php
require_once __DIR__ . '/config/Database.php';

$userIdHash = filter_input(INPUT_GET, 'u');
$userId = $userIdHash ? (int)base64_decode($userIdHash) : 0;

if (!$userId) {
    header('Location: index.php?pagina=auth-login');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET recebe_alertas = 0 WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        
        // Setup a flash message to login if desired, or just redirect
        header('Location: index.php?pagina=auth-login&msg=optout_success');
        exit;
    } catch (Exception $e) {
        $erro = 'Ocorreu um erro ao processar sua solicitação. Tente novamente mais tarde.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar Inscrição - Gerenciador de Contas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        darkbg: '#121212',
                        darkcard: '#1e1e1e',
                        darkborder: '#333333'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-darkbg min-h-screen flex items-center justify-center p-4 transition-colors">
    <div class="max-w-md w-full bg-white dark:bg-darkcard rounded-2xl shadow-xl border border-gray-100 dark:border-darkborder p-8 text-center">
        
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">
            <i class="fa-regular fa-bell-slash"></i>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Cancelar Alertas</h2>
        
        <?php if ($erro): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm text-left">
                <i class="fa-solid fa-circle-exclamation mr-2"></i> <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
            Deseja realmente parar de receber os alertas diários de vencimentos financeiros no seu e-mail? 
            Você poderá reativá-los a qualquer momento pelo seu painel de usuário.
        </p>

        <form method="POST" action="cancelar_inscricao.php?u=<?php echo htmlspecialchars($userIdHash); ?>" class="space-y-3">
            <button type="submit" class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors shadow-sm focus:ring-4 focus:ring-red-500/20">
                Sim, cancelar recebimento
            </button>
            <a href="index.php?pagina=auth-login" class="block w-full py-3 px-4 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors text-center">
                Não, manter meus alertas
            </a>
        </form>

    </div>
</body>
</html>
