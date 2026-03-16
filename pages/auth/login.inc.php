<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Controle Familiar</title>
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
                        primary: '#3b82f6'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 dark:bg-darkbg text-gray-900 dark:text-gray-100 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="bg-white dark:bg-darkcard w-full max-w-md rounded-2xl shadow-xl border border-gray-100 dark:border-darkborder overflow-hidden">
        <div class="p-8 text-center bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-darkborder">
            <div class="h-16 w-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-wallet text-3xl text-primary"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Controle Familiar</h1>
            <p class="text-sm text-gray-500 mt-2">Faça o login para gerenciar suas contas</p>
        </div>

        <div class="p-8">
            <form id="form-login" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">E-mail</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required placeholder="admin@familia.com"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-darkborder rounded-lg bg-white dark:bg-[#121212] focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-gray-900 dark:text-gray-100"
                            placeholder="seu@email.com">
                    </div>
                </div>

                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="senha" name="senha" required placeholder="••••••••"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-darkborder rounded-lg bg-white dark:bg-[#121212] focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-gray-900 dark:text-gray-100"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" id="btn-login"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors disabled:opacity-50">
                    Entrar
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/auth.js"></script>
</body>
</html>
