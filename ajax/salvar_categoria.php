<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// Validar sessão
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Sessão expirada.']);
    exit;
}

require_once __DIR__ . '/../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../src/Repositories/CategoriaRepository.php';

$nome = trim($_POST['nome'] ?? '');

if (empty($nome)) {
    echo json_encode(['sucesso' => false, 'erro' => 'O nome da categoria é obrigatório.']);
    exit;
}

$instituicao_id = AuthHelper::getInstituicaoId();
$repo = new CategoriaRepository();

$dados = ['nome' => $nome];

if ($repo->salvar($instituicao_id, $dados)) {
    // Pegar o ID do último insert
    // Como o Repository usa a mesma conexão PDO, podemos acessar o database
    $db = Database::getConnection();
    echo json_encode([
        'sucesso' => true, 
        'id' => $db->lastInsertId(), 
        'nome' => $nome
    ]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao salvar categoria no banco de dados.']);
}
