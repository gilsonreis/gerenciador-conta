<?php
// Força o retorno estrito em JSON
header('Content-Type: application/json; charset=utf-8');

// Inicia a sessão para controle de acesso
session_start();

// 1. Segurança Primária: Bloqueia acesso direto via navegador
// Aceita apenas requisições que venham do jQuery/Axios/Fetch (XHR)
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado. Tipo de requisição não permitida.']);
    exit;
}

// 2. Coleta da ação solicitada (ex: despesas-buscar)
$acao = filter_input(INPUT_GET, 'acao', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$acao || strpos($acao, '-') === false) {
    http_response_code(400);
    echo json_encode(['erro' => 'Formato de ação inválido. Utilize o padrão diretorio-arquivo.']);
    exit;
}

// 3. Middleware de Autenticação
// Garante que o usuário tem uma sessão ativa, exceto se estiver tentando fazer login
if (!isset($_SESSION['usuario_id']) && $acao !== 'auth-login') {
    http_response_code(401);
    // Retorna a flag de redirect para o jQuery forçar o recarregamento da página para a tela de login
    echo json_encode(['erro' => 'Sessão expirada ou não autenticada.', 'redirect' => '?pagina=auth-login']);
    exit;
}

// 4. Tratamento de Rota e Prevenção contra LFI (Local File Inclusion)
list($diretorio, $arquivo) = explode('-', $acao, 2);

// Limpa qualquer caractere malicioso, permitindo apenas letras, números e underscores
$diretorio = preg_replace('/[^a-zA-Z0-9_]/', '', $diretorio);
$arquivo = preg_replace('/[^a-zA-Z0-9_]/', '', $arquivo);

$caminhoArquivo = __DIR__ . "/endpoints/{$diretorio}/{$arquivo}.php";

// 5. Execução do Endpoint
if (file_exists($caminhoArquivo)) {
    try {
        // O arquivo carregado aqui dentro usará as classes Repository
        // e fará o echo json_encode() com a resposta.
        require_once $caminhoArquivo;
    } catch (Throwable $e) {
        // Evita que erros fatais do PHP quebrem o JSON no frontend
        http_response_code(500);
        echo json_encode([
            'erro' => 'Erro interno no servidor.',
            'detalhe' => $e->getMessage() // Em produção, idealmente ocultar isso ou colocar debug level
        ]);
    }
} else {
    http_response_code(404);
    echo json_encode(['erro' => "O endpoint solicitado ({$acao}) não foi encontrado."]);
}
