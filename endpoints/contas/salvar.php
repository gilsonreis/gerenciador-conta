<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';
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

$dados      = $_POST;
$id         = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
$dados['id']= $id;

if (empty(trim($dados['nome'] ?? ''))) {
    http_response_code(400);
    echo json_encode(['erro' => 'O nome da conta é obrigatório.']);
    exit;
}

$repo = new ContaRepository();
if ($repo->salvar($instituicaoId, $dados)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Conta salva com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao salvar a conta']);
}
