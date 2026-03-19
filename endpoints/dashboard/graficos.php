<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';

AuthHelper::requireLogin();
$instituicaoId = AuthHelper::getInstituicaoId();
$db = Database::getConnection();

function getMesesFuturos(int $qtd = 12): array {
    $meses = [];
    $data = new DateTime('first day of this month');
    for ($i = 0; $i < $qtd; $i++) {
        $meses[$data->format('Y-m')] = 0.00;
        $data->modify('+1 month');
    }
    return $meses;
}

function getMesesPassados(int $qtd = 12): array {
    $meses = [];
    $data = new DateTime('first day of this month');
    $data->modify('-' . ($qtd - 1) . ' months'); // include current month
    for ($i = 0; $i < $qtd; $i++) {
        $meses[$data->format('Y-m')] = 0.00;
        $data->modify('+1 month');
    }
    return $meses;
}

$mesesAbreviados = ['01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'];
function formatarLabel(string $anoMes) {
    global $mesesAbreviados;
    list($ano, $mes) = explode('-', $anoMes);
    return $mesesAbreviados[$mes] . '/' . substr($ano, 2);
}

// Helpers: WHERE condicional por instituição; inst=0 = super_admin (sem filtro)
$instWhereEnt = $instituicaoId === 0 ? '' : 'AND instituicao_id = :inst';
$instWhereSai = $instituicaoId === 0 ? '' : 'AND l.instituicao_id = :inst';

// 1. Histórico (-12)
$histBaseEntradas = getMesesPassados(12);
$histBaseSaidas   = getMesesPassados(12);
$dataInicioHist   = key($histBaseEntradas) . '-01';

$paramsBase = ['inicio' => $dataInicioHist];
if ($instituicaoId !== 0) $paramsBase['inst'] = $instituicaoId;

$sqlEntradas = "
    SELECT DATE_FORMAT(data_entrada, '%Y-%m') as mes, SUM(valor) as total
    FROM caixa_entradas
    WHERE data_entrada >= :inicio
    $instWhereEnt
    GROUP BY mes
";
$stmt = $db->prepare($sqlEntradas);
$stmt->execute($paramsBase);
foreach($stmt->fetchAll() as $row) {
    if(isset($histBaseEntradas[$row['mes']])) $histBaseEntradas[$row['mes']] = (float)$row['total'];
}

$sqlSaidas = "
    SELECT DATE_FORMAT(p.data_pagamento, '%Y-%m') as mes, SUM(p.valor - p.desconto) as total
    FROM parcelas p
    JOIN lancamentos l ON p.lancamento_id = l.id
    WHERE p.data_pagamento IS NOT NULL AND p.data_pagamento >= :inicio
    $instWhereSai
    GROUP BY mes
";
$stmt = $db->prepare($sqlSaidas);
$stmt->execute($paramsBase);
foreach($stmt->fetchAll() as $row) {
    if(isset($histBaseSaidas[$row['mes']])) $histBaseSaidas[$row['mes']] = (float)$row['total'];
}

// 2. Projeção (+12)
$projBaseSaidas   = getMesesFuturos(12);
$dataInicioProj   = date('Y-m-01');
$dataFimProjDate  = new DateTime('last day of this month');
$dataFimProjDate->modify('+11 months');
$dataFimProj = $dataFimProjDate->format('Y-m-d');

$paramsProj = ['inicio' => $dataInicioProj, 'fim' => $dataFimProj];
if ($instituicaoId !== 0) $paramsProj['inst'] = $instituicaoId;

$sqlProj = "
    SELECT DATE_FORMAT(p.data_vencimento, '%Y-%m') as mes, SUM(p.valor) as total
    FROM parcelas p
    JOIN lancamentos l ON p.lancamento_id = l.id
    WHERE p.data_vencimento >= :inicio AND p.data_vencimento <= :fim
    $instWhereSai
    GROUP BY mes
";
$stmt = $db->prepare($sqlProj);
$stmt->execute($paramsProj);
foreach($stmt->fetchAll() as $row) {
    if(isset($projBaseSaidas[$row['mes']])) $projBaseSaidas[$row['mes']] = (float)$row['total'];
}

// Consolidação e Output
$response = [
    'historico' => [
        'labels'   => array_map('formatarLabel', array_keys($histBaseEntradas)),
        'entradas' => array_values($histBaseEntradas),
        'saidas'   => array_values($histBaseSaidas)
    ],
    'projecao' => [
        'labels' => array_map('formatarLabel', array_keys($projBaseSaidas)),
        'saidas' => array_values($projBaseSaidas)
    ]
];

echo json_encode(['sucesso' => true, 'dados' => $response]);
exit;
