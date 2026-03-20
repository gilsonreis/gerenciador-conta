<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/ContaRepository.php';

AuthHelper::requireLogin();

$instId       = AuthHelper::getInstituicaoId();
$isSuperAdmin = $instId === 0;
$repo         = new ContaRepository();

if ($isSuperAdmin) {
    // Super admin: busca todas com JOIN para nome da instituição
    $filtroInst = filter_input(INPUT_GET, 'instituicao_id', FILTER_VALIDATE_INT) ?: 0;
    $db = Database::getConnection();
    $where = $filtroInst ? 'WHERE c.instituicao_id = :inst' : '';
    $params = $filtroInst ? ['inst' => $filtroInst] : [];
    $stmt = $db->prepare("
        SELECT c.*, i.nome as instituicao_nome
        FROM contas c
        LEFT JOIN instituicoes i ON c.instituicao_id = i.id
        $where
        ORDER BY i.nome ASC, c.nome ASC
    ");
    $stmt->execute($params);
    $contas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $contas = $repo->listar($instId);
}

foreach ($contas as &$conta) {
    if (isset($conta['saldo_inicial'])) {
        $conta['saldo_inicial_formatado'] = number_format((float)$conta['saldo_inicial'], 2, ',', '.');
    }
}

echo json_encode([
    'sucesso'       => true,
    'dados'         => $contas,
    'is_super_admin'=> $isSuperAdmin,
]);
