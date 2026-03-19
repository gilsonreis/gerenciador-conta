<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/InstituicaoRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('instituicoes_write');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

// Security: Prevent users from deleting the institution they are currently logged into
// Note: Depending on business rules, you might allow this and log them out,
// but usually it's destructive. For now, we block deleting their current institution.
if ($id === AuthHelper::getInstituicaoId()) {
     http_response_code(403);
     echo json_encode(['erro' => 'Você não pode excluir a instituição na qual está atualmente logado.']);
     exit;
}

$repo = new InstituicaoRepository();

try {
    if ($repo->excluir(AuthHelper::getUsuarioId(), $id)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Instituição excluída com sucesso']);
    } else {
        http_response_code(403);
        echo json_encode(['erro' => 'Acesso negado. Você não tem permissão para excluir esta instituição ou ela não foi encontrada.']);
    }
} catch (PDOException $e) {
    http_response_code(409);
    echo json_encode(['erro' => 'Não é possível excluir esta instituição pois ela possui dados vinculados (usuários, lançamentos, etc).']);
}
