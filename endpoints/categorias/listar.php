<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CategoriaRepository.php';

AuthHelper::requireLogin();
$repo = new CategoriaRepository();
$categorias = $repo->listar(AuthHelper::getInstituicaoId());

echo json_encode(['sucesso' => true, 'dados' => $categorias]);
