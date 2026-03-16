<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/ParcelaRepository.php';

AuthHelper::requireLogin();

$parcelaId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$dados = $_POST;

// Validação basica
if (empty($dados['descricao']) || empty($dados['categoria_id']) || empty($dados['valor']) || empty($dados['data_vencimento'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Preencha os campos obrigatórios (Descrição, Categoria, Valor e Vencimento).']);
    exit;
}

if ($parcelaId) {
    // Modo Edição: Atualiza Apenas a Parcela e os dados do Lançamento Pai
    $repo = new ParcelaRepository();
    if ($repo->atualizar(AuthHelper::getInstituicaoId(), $parcelaId, $dados)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Parcela atualizada com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao atualizar parcela.']);
    }
} else {
    // Modo Inserção: Cria Pai e N Filhos parcelados
    $repo = new LancamentoRepository();
    try {
        if ($repo->salvarComParcelas(AuthHelper::getInstituicaoId(), AuthHelper::getUsuarioId(), $dados)) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Despesa salva com sucesso.']);
        } else {
            http_response_code(500);
            echo json_encode(['erro' => 'Falha ao inserir lançamentos.']);
        }
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro interno ao parcelar.', 'detalhe' => $e->getMessage()]);
    }
}
