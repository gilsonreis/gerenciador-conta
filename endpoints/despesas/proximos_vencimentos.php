<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../config/Database.php';

AuthHelper::requireLogin();
$instituicaoId = AuthHelper::getInstituicaoId();
$db = Database::getConnection();

// Radar de 30 dias: parcelas não pagas com vencimento entre hoje e +30 dias
$sql = "
    SELECT
        p.id,
        l.descricao,
        p.data_vencimento,
        p.valor,
        p.desconto,
        cat.nome as categoria_nome,
        DATEDIFF(p.data_vencimento, CURDATE()) as dias_restantes
    FROM parcelas p
    JOIN lancamentos l ON p.lancamento_id = l.id
    JOIN categorias cat ON l.categoria_id = cat.id
    WHERE l.instituicao_id = :inst
    AND p.data_pagamento IS NULL
    AND p.data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY p.data_vencimento ASC
    LIMIT 8
";

$stmt = $db->prepare($sql);
$stmt->execute(['inst' => $instituicaoId]);
$vencimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$resultado = array_map(function($v) {
    $valor = (float)$v['valor'] - (float)$v['desconto'];
    $diasRestantes = (int)$v['dias_restantes'];

    // Urgência visual: vence hoje/amanhã = vermelho, 2-7 dias = laranja, 8+ = cinza
    if ($diasRestantes === 0) {
        $urgencia = 'hoje';
    } elseif ($diasRestantes <= 2) {
        $urgencia = 'critico';
    } elseif ($diasRestantes <= 7) {
        $urgencia = 'alerta';
    } else {
        $urgencia = 'normal';
    }

    return [
        'id'              => $v['id'],
        'descricao'       => $v['descricao'],
        'categoria'       => $v['categoria_nome'],
        'data_vencimento' => $v['data_vencimento'],
        'data_formatada'  => date('d/m/Y', strtotime($v['data_vencimento'])),
        'valor'           => $valor,
        'valor_formatado' => 'R$ ' . number_format($valor, 2, ',', '.'),
        'dias_restantes'  => $diasRestantes,
        'urgencia'        => $urgencia,
    ];
}, $vencimentos);

echo json_encode(['sucesso' => true, 'dados' => $resultado, 'total' => count($resultado)]);
