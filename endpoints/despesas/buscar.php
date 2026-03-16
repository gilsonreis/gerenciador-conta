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
    $dados = $repo->buscarLancamentoEParcelas(AuthHelper::getInstituicaoId(), $lancamentoId);
    if ($dados) {
        if (!empty($dados['parcelas'])) {
            foreach($dados['parcelas'] as &$p) {
                $p['valor'] = number_format((float)$p['valor'], 2, ',', '.');
            }
        }
        echo json_encode([
            'sucesso' => true, 
            'lancamento' => $dados['lancamento'], 
            'parcelas' => $dados['parcelas']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Lançamento/Parcelas não encontradas']);
    }
} else {
    $parcela = $repo->buscar(AuthHelper::getInstituicaoId(), $parcelaId);
    if ($parcela) {
        $parcela['valor'] = number_format((float)$parcela['valor'], 2, ',', '.');
        echo json_encode(['sucesso' => true, 'dados' => $parcela]);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Despesa/Parcela não encontrada']);
    }
}
