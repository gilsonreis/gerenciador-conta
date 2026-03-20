<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/TransferenciaRepository.php';

AuthHelper::requireLogin();

$instId       = AuthHelper::getInstituicaoId();
$isSuperAdmin = $instId === 0;

if ($isSuperAdmin) {
    $filtroInst  = filter_input(INPUT_GET, 'filtro_instituicao_id', FILTER_VALIDATE_INT) ?: 0;
    $filtroWhere = $filtroInst ? 'AND t.instituicao_id = :filtro_inst' : '';
    $params      = $filtroInst ? ['filtro_inst' => $filtroInst] : [];

    $db   = Database::getConnection();
    $stmt = $db->prepare("
        SELECT
            t.*,
            co.nome  as conta_origem_nome,
            cd.nome  as conta_destino_nome,
            i.nome   as instituicao_nome
        FROM transferencias t
        JOIN contas co       ON t.conta_origem_id  = co.id
        JOIN contas cd       ON t.conta_destino_id = cd.id
        JOIN instituicoes i  ON t.instituicao_id   = i.id
        WHERE 1=1 $filtroWhere
        ORDER BY t.data_transferencia DESC
    ");
    $stmt->execute($params);
    $transferencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $repo           = new TransferenciaRepository();
    $transferencias = $repo->listar($instId);
}

foreach ($transferencias as &$t) {
    $t['valor_formatado'] = 'R$ ' . number_format((float)$t['valor'], 2, ',', '.');
    $t['data_formatada']  = date('d/m/Y', strtotime($t['data_transferencia']));
}

echo json_encode([
    'sucesso'        => true,
    'dados'          => $transferencias,
    'is_super_admin' => $isSuperAdmin,
]);
