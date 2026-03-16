<?php
// ajax.php garante json e sessão
require_once __DIR__ . '/../../src/Repositories/UsuarioRepository.php';

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    http_response_code(400);
    echo json_encode(['erro' => 'E-mail e senha são obrigatórios.']);
    exit;
}

$repo = new UsuarioRepository();
$usuario = $repo->autenticar($email, $senha);

if ($usuario) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['instituicao_id'] = $usuario['instituicao_id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    
    echo json_encode(['sucesso' => true]);
} else {
    http_response_code(401);
    echo json_encode(['erro' => 'Credenciais inválidas.']);
}
