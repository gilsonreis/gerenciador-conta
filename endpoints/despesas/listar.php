<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';

AuthHelper::requireLogin();

$isSuperAdmin = AuthHelper::getInstituicaoId() === 0;

$mesAno = $_GET['mes'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $mesAno)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Formato de mês inválido. Use YYYY-MM']);
    exit;
}

$categoriaId = filter_input(INPUT_GET, 'categoria_id', FILTER_VALIDATE_INT) ?: null;
$contaFixa = filter_input(INPUT_GET, 'conta_fixa');
$contaFixaValue = ($contaFixa === '0' || $contaFixa === '1') ? (int)$contaFixa : null;
$busca = filter_input(INPUT_GET, 'busca_descricao', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
$filtroInstituicaoId = $isSuperAdmin ? (filter_input(INPUT_GET, 'filtro_instituicao_id', FILTER_VALIDATE_INT) ?: null) : null;

$itensPorPagina = 10;
$paginaAtual = filter_input(INPUT_GET, 'p', FILTER_VALIDATE_INT) ?: 1;
if ($paginaAtual < 1) $paginaAtual = 1;

$repo = new LancamentoRepository();
$resultado = $repo->listarPorMes(AuthHelper::getInstituicaoId(), $mesAno, $categoriaId, $contaFixaValue, $paginaAtual, $itensPorPagina, $busca, $filtroInstituicaoId);
$resumo    = $repo->resumoMes(AuthHelper::getInstituicaoId(), $mesAno, $categoriaId, $contaFixaValue, $busca, $filtroInstituicaoId);

$totalPaginas = ceil($resultado['total'] / $itensPorPagina);

echo json_encode([
    'sucesso'        => true,
    'dados'          => $resultado['dados'],
    'is_super_admin' => $isSuperAdmin,
    'paginacao' => [
        'pagina_atual'     => $paginaAtual,
        'total_paginas'    => $totalPaginas,
        'total_registros'  => $resultado['total'],
        'itens_por_pagina' => $itensPorPagina
    ],
    'resumo' => [
        'total_saidas'           => (float)($resumo['total_saidas'] ?? 0),
        'total_saidas_formatado' => 'R$ ' . number_format((float)($resumo['total_saidas'] ?? 0), 2, ',', '.'),
        'custo_vida'             => (float)($resumo['custo_vida'] ?? 0),
        'custo_vida_formatado'   => 'R$ ' . number_format((float)($resumo['custo_vida'] ?? 0), 2, ',', '.')
    ]
]);
