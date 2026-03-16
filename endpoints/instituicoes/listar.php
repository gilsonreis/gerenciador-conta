<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/InstituicaoRepository.php';

AuthHelper::requireLogin();
$repo = new InstituicaoRepository();

// Pass the logged in user ID to ensure they only list the institution they belong to
$instituicoes = $repo->listar(AuthHelper::getUsuarioId());

echo json_encode(['sucesso' => true, 'dados' => $instituicoes]);
