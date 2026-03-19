<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/InstituicaoRepository.php';

AuthHelper::requireLogin();
$repo = new InstituicaoRepository();

// Super admin (inst=0): lista todas as instituições passando userId=0.
// Demais usuários: filtra pela instituição do usuário logado.
$userId = AuthHelper::getInstituicaoId() === 0 ? 0 : AuthHelper::getUsuarioId();
$instituicoes = $repo->listar($userId);

echo json_encode(['sucesso' => true, 'dados' => $instituicoes]);
