<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/UsuarioRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('usuarios');

// Regra RBAC: determina a institução correta para o novo usuário
$isSuperAdmin = AuthHelper::getInstituicaoId() === 0;
if ($isSuperAdmin) {
    // Super admin escolhe a instituição via POST (vem do select do form)
    $instituicaoId = filter_input(INPUT_POST, 'instituicao_id', FILTER_VALIDATE_INT) ?: 0;
    if (empty($instituicaoId)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Selecione uma Instituição para o usuário.']);
        exit;
    }
} else {
    // Admin/gestor: sempre usa a própria instituição da sessão
    $instituicaoId = AuthHelper::getInstituicaoIdReal();
}

$nome  = trim($_POST['nome']  ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$id    = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
$role  = $_POST['role'] ?? 'admin';

// ── Blindagem de escalada de privilégio ───────────────────────────────
// Mapeia quais roles o usuário logado pode atribuir
$callerRole = AuthHelper::getRole();
$rolesPermitidos = match($callerRole) {
    'super_admin' => ['super_admin', 'admin', 'manager', 'reader'],
    'admin'       => ['admin', 'manager', 'reader'],
    default       => [], // gestor/leitor não cadastram ninguém (AclService já bloqueia)
};

if (!in_array($role, $rolesPermitidos, true)) {
    http_response_code(403);
    echo json_encode(['erro' => 'Você não tem permissão para atribuir o perfil selecionado.']);
    exit;
}
// ──────────────────────────────────────────────────────────────────────

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
    'id'    => $id,
    'nome'  => $nome,
    'email' => $email,
    'senha' => $senha,
    'role'  => $role,
];

$repo = new UsuarioRepository();

try {
    if ($repo->salvar($instituicaoId, $dados)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário salvo com sucesso']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao salvar o usuário']);
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        // Diferencia violação de UNIQUE (email duplicado) vs FK (instituição inválida)
        if (str_contains($e->getMessage(), 'email') || str_contains($e->getMessage(), 'Duplicate')) {
            http_response_code(409);
            echo json_encode(['erro' => 'Este e-mail já está sendo utilizado.']);
        } else {
            http_response_code(400);
            echo json_encode(['erro' => 'Instituição inválida ou usuário sem vínculo.']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro no banco de dados.', 'detalhe' => $e->getMessage()]);
    }
}
