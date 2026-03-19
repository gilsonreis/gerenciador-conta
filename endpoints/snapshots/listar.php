<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../config/Database.php';

AuthHelper::requireLogin();
$instituicaoId = AuthHelper::getInstituicaoId();
$db = Database::getConnection();

$ano = filter_input(INPUT_GET, 'ano', FILTER_VALIDATE_INT) ?: null;

$params = ['inst' => $instituicaoId];
$whereAno = '';
if ($ano) {
    $whereAno = "AND YEAR(s.mes_referencia) = :ano";
    $params['ano'] = $ano;
}

// Listar os anos disponíveis para o filtro
$sqlAnos = "
    SELECT DISTINCT YEAR(s.mes_referencia) as ano
    FROM snapshots_saldos s
    JOIN contas c ON s.conta_id = c.id
    WHERE c.instituicao_id = :inst
    ORDER BY ano DESC
";
$stmtAnos = $db->prepare($sqlAnos);
$stmtAnos->execute(['inst' => $instituicaoId]);
$anos = $stmtAnos->fetchAll(PDO::FETCH_COLUMN);

// Listar snapshots com JOIN em contas
$sql = "
    SELECT
        s.id,
        c.nome AS conta_nome,
        DATE_FORMAT(s.mes_referencia, '%m/%Y') AS mes_formatado,
        s.mes_referencia,
        s.valor_abertura,
        s.criado_em
    FROM snapshots_saldos s
    JOIN contas c ON s.conta_id = c.id
    WHERE c.instituicao_id = :inst
    $whereAno
    ORDER BY s.mes_referencia DESC, c.nome ASC
";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$snapshots = $stmt->fetchAll(PDO::FETCH_ASSOC);

$resultado = array_map(function($row) {
    return [
        'id'              => $row['id'],
        'conta_nome'      => $row['conta_nome'],
        'mes_formatado'   => $row['mes_formatado'],
        'mes_referencia'  => $row['mes_referencia'],
        'valor_abertura'  => (float)$row['valor_abertura'],
        'valor_formatado' => 'R$ ' . number_format((float)$row['valor_abertura'], 2, ',', '.'),
        'criado_em'       => date('d/m/Y H:i', strtotime($row['criado_em'])),
    ];
}, $snapshots);

echo json_encode([
    'sucesso'   => true,
    'dados'     => $resultado,
    'anos'      => $anos,
    'total'     => count($resultado),
]);
