<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/UsuarioRepository.php';

AuthHelper::requireLogin();
$repo = new UsuarioRepository();
$usuarios = $repo->listar(AuthHelper::getInstituicaoId());

echo json_encode(['sucesso' => true, 'dados' => $usuarios]);
