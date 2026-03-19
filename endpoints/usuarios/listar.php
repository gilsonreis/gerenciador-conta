<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/UsuarioRepository.php';

AuthHelper::requireLogin();

$repo = new UsuarioRepository();

// Super admin pode passar ?instituicao_id=X para filtrar usuários de uma instituição específica
// (usado no select de contexto do formulário de despesas)
$isSuperAdmin = AuthHelper::getInstituicaoId() === 0;
if ($isSuperAdmin) {
    $instFiltro = filter_input(INPUT_GET, 'instituicao_id', FILTER_VALIDATE_INT) ?: 0;
} else {
    $instFiltro = AuthHelper::getInstituicaoId();
}

$usuarios = $repo->listar($instFiltro);

// Sempre remove super_admins da listagem (não devem aparecer como opção de responsável)
$usuarios = array_values(array_filter($usuarios, fn($u) => ($u['role'] ?? 'admin') !== 'super_admin'));

echo json_encode(['sucesso' => true, 'dados' => $usuarios]);
