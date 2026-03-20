<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CaixaRepository.php';

AuthHelper::requireLogin();

$mesAno = $_GET['mes'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $mesAno)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Formato de mês inválido. Use YYYY-MM']);
    exit;
}

$instId       = AuthHelper::getInstituicaoId();
$isSuperAdmin = $instId === 0;
$repo         = new CaixaRepository();

if ($isSuperAdmin) {
    $filtroInst    = filter_input(INPUT_GET, 'filtro_instituicao_id', FILTER_VALIDATE_INT) ?: 0;
    $filtroWhere   = $filtroInst ? 'AND ce.instituicao_id = :filtro_inst' : '';
    $params        = ['mes' => $mesAno];
    if ($filtroInst) $params['filtro_inst'] = $filtroInst;

    $db   = Database::getConnection();
    $stmt = $db->prepare("
        SELECT
            ce.id,
            ce.conta_id,
            co.nome as conta_nome,
            ce.descricao,
            ce.valor,
            ce.data_entrada,
            i.nome  as instituicao_nome,
            u.nome  as usuario_nome
        FROM caixa_entradas ce
        JOIN contas co       ON ce.conta_id      = co.id
        JOIN instituicoes i  ON ce.instituicao_id = i.id
        JOIN usuarios u      ON ce.usuario_id     = u.id
        WHERE DATE_FORMAT(ce.data_entrada, '%Y-%m') = :mes
        $filtroWhere
        ORDER BY ce.data_entrada ASC
    ");
    $stmt->execute($params);
    $entradas      = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalEntradas = array_sum(array_column($entradas, 'valor'));
} else {
    $entradas      = $repo->listarPorMes($instId, $mesAno);
    $totalEntradas = $repo->resumoMes($instId, $mesAno);
}

echo json_encode([
    'sucesso'        => true,
    'dados'          => $entradas,
    'is_super_admin' => $isSuperAdmin,
    'resumo'         => [
        'total_entradas'           => (float)$totalEntradas,
        'total_entradas_formatado' => 'R$ ' . number_format((float)$totalEntradas, 2, ',', '.'),
    ],
]);
