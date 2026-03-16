<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();

$repo = new ContaRepository();
$contas = $repo->listar(AuthHelper::getInstituicaoId());

// Format saldo for list display if needed
foreach ($contas as &$conta) {
    if (isset($conta['saldo_inicial'])) {
        $conta['saldo_inicial_formatado'] = number_format((float)$conta['saldo_inicial'], 2, ',', '.');
    }
}

echo json_encode(['sucesso' => true, 'dados' => $contas]);
