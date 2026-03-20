<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CategoriaRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('write');

// Super admin escolhe a instituição; demais usam a sessão
$isSuperAdmin = AuthHelper::getInstituicaoId() === 0;
if ($isSuperAdmin) {
    $instituicaoId = filter_input(INPUT_POST, 'instituicao_id', FILTER_VALIDATE_INT) ?: 0;
    if (!$instituicaoId) {
        http_response_code(400);
        echo json_encode(['erro' => 'Selecione uma Instituição.']);
        exit;
    }
} else {
    $instituicaoId = AuthHelper::getInstituicaoIdReal();
}

$nome = trim($_POST['nome'] ?? '');
if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome da categoria é obrigatório']);
    exit;
}

$dados = [
    'id'   => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null,
    'nome' => $nome
];

$repo = new CategoriaRepository();
if ($repo->salvar($instituicaoId, $dados)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Categoria salva com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao salvar categoria']);
}
