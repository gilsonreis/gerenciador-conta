<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';
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

$usuarioId   = AuthHelper::getUsuarioId();
$contaId     = filter_input(INPUT_POST, 'conta_id', FILTER_VALIDATE_INT);
$valor_raw   = trim($_POST['valor'] ?? '');
$dataEntrada = trim($_POST['data_entrada'] ?? '');
$id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;

if (!$contaId || empty($valor_raw) || empty($dataEntrada)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Todos os campos são obrigatórios.']);
    exit;
}

$descricao = trim($_POST['descricao'] ?? '');

$dados = [
    'id'          => $id,
    'conta_id'    => $contaId,
    'valor'       => $valor_raw,
    'descricao'   => $descricao,
    'data_entrada'=> $dataEntrada
];

$repo = new CaixaRepository();
if ($repo->salvar($instituicaoId, $usuarioId, $dados)) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Entrada salva com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao salvar a entrada']);
}
