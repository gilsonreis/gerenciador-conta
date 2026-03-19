<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/UsuarioRepository.php';

AuthHelper::requireLogin();

$repo = new UsuarioRepository();
$usuarios = $repo->listar(AuthHelper::getInstituicaoId());

// RBAC: admin não enxerga super_admins na listagem
if (AuthHelper::getRole() !== 'super_admin') {
    $usuarios = array_values(array_filter($usuarios, fn($u) => ($u['role'] ?? 'admin') !== 'super_admin'));
}

echo json_encode(['sucesso' => true, 'dados' => $usuarios]);
