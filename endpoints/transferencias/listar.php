<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/TransferenciaRepository.php';

AuthHelper::requireLogin();

$repo = new TransferenciaRepository();
$transferencias = $repo->listar(AuthHelper::getInstituicaoId());

// Formatação para exibição
foreach ($transferencias as &$t) {
    $t['valor_formatado'] = 'R$ ' . number_format((float)$t['valor'], 2, ',', '.');
    $t['data_formatada'] = date('d/m/Y', strtotime($t['data_transferencia']));
}

echo json_encode(['sucesso' => true, 'dados' => $transferencias]);
