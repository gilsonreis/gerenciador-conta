<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';

AuthHelper::requireLogin();

$lancamentoId = filter_input(INPUT_GET, 'lancamento_id', FILTER_VALIDATE_INT);
$parcelaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$lancamentoId && !$parcelaId) {
    http_response_code(400);
    echo json_encode(['erro' => 'Id inválido']);
    exit;
}

$repo = new LancamentoRepository();

if ($lancamentoId) {
    $busca = filter_input(INPUT_GET, 'busca_parcela', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';
    $pagina = filter_input(INPUT_GET, 'p', FILTER_VALIDATE_INT) ?: 1;
    $itensPorPagina = 12;

    $dados = $repo->buscarLancamentoEParcelas(AuthHelper::getInstituicaoId(), $lancamentoId);
    
    if ($dados) {
        $paginado = $repo->listarParcelasPai($lancamentoId, $busca, $pagina, $itensPorPagina);
        
        echo json_encode([
            'sucesso' => true, 
            'lancamento' => $dados['lancamento'], 
            'parcelas' => $paginado['parcelas'],
            'paginacao' => [
                'total' => $paginado['total'],
                'pagina' => $paginado['pagina'],
                'total_paginas' => $paginado['total_paginas'],
                'itens_por_pagina' => $itensPorPagina
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Lançamento/Parcelas não encontradas']);
    }
} else {
    $parcela = $repo->buscar(AuthHelper::getInstituicaoId(), $parcelaId);
    if ($parcela) {
        echo json_encode(['sucesso' => true, 'dados' => $parcela]);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Despesa/Parcela não encontrada']);
    }
}
