<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../config/Database.php';

AuthHelper::requireLogin();

$usuarioId     = AuthHelper::getUsuarioId();
$instituicaoId = AuthHelper::getInstituicaoId();
$db            = Database::getConnection();

// ── GET: retorna dados do perfil ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($instituicaoId === 0) {
        $stmt = $db->prepare("SELECT id, nome, email, recebe_alertas FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $usuarioId]);
    } else {
        $stmt = $db->prepare("SELECT id, nome, email, recebe_alertas FROM usuarios WHERE id = :id AND instituicao_id = :inst");
        $stmt->execute(['id' => $usuarioId, 'inst' => $instituicaoId]);
    }
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado.']);
        exit;
    }

    echo json_encode(['sucesso' => true, 'dados' => $usuario]);
    exit;
}

// ── POST: salva alterações do perfil ────────────────────────
$nome           = trim($_POST['nome'] ?? '');
$senha          = $_POST['senha'] ?? '';
$recebeAlertas  = isset($_POST['recebe_alertas']) ? 1 : 0;

if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O nome não pode ser vazio.']);
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
$db = Database::getConnection();

if (!empty($senha)) {
    $stmt = $db->prepare("UPDATE usuarios SET nome = :nome, senha = :senha, recebe_alertas = :alertas WHERE id = :id AND instituicao_id = :inst");
    $stmt->execute([
        'nome'   => $nome,
        'senha'  => password_hash($senha, PASSWORD_DEFAULT),
        'alertas'=> $recebeAlertas,
        'id'     => $usuarioId,
        'inst'   => $instituicaoId,
    ]);
} else {
    $stmt = $db->prepare("UPDATE usuarios SET nome = :nome, recebe_alertas = :alertas WHERE id = :id AND instituicao_id = :inst");
    $stmt->execute([
        'nome'   => $nome,
        'alertas'=> $recebeAlertas,
        'id'     => $usuarioId,
        'inst'   => $instituicaoId,
    ]);
}

// Atualiza o nome na sessão imediatamente
$_SESSION['usuario_nome'] = $nome;

echo json_encode(['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso!', 'nome' => $nome]);
