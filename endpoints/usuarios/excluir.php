<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/UsuarioRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('usuarios');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

if ($id === AuthHelper::getUsuarioId()) {
    http_response_code(403);
    echo json_encode(['erro' => 'Você não pode excluir a si mesmo enquanto logado.']);
    exit;
}

$repo = new UsuarioRepository();
try {
    if ($repo->excluir(AuthHelper::getInstituicaoId(), $id)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso']);
    } else {
        http_response_code(400);
        echo json_encode(['erro' => 'Falha ao excluir o usuário']);
    }
} catch (PDOException $e) {
    http_response_code(409);
    echo json_encode(['erro' => 'Não é possível excluir este usuário. Ele possui vínculos com lançamentos ou caixas.']);
}
