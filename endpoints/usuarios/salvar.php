<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/UsuarioRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('usuarios');

// Regra RBAC: admin não pode escolher a instituição do novo usuário
// A instituição é sempre a do próprio admin logado
$instituicaoId = AuthHelper::getInstituicaoId();

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? ''; // Can be empty if editing and preserving previous password
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;

if (empty($nome) || empty($email)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome e Email são obrigatórios']);
    exit;
}

if (!$id && empty($senha)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Senha é obrigatória para novos usuários']);
    exit;
}

$dados = [
    'id' => $id,
    'nome' => $nome,
    'email' => $email,
    'senha' => $senha
];

$repo = new UsuarioRepository();

try {
    if ($repo->salvar(AuthHelper::getInstituicaoId(), $dados)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário salvo com sucesso']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao salvar o usuário']);
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // UNIQUE constraint
        http_response_code(409);
        echo json_encode(['erro' => 'Este e-mail já está sendo utilizado.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro no banco de dados.']);
    }
}
