<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/ParcelaRepository.php';

AuthHelper::requireLogin();

$lancamentoId = filter_input(INPUT_POST, 'lancamento_id', FILTER_VALIDATE_INT);
$dados = $_POST;

if ($lancamentoId) {
    // Modo Edição: Atualiza Apenas os dados PAI do Lançamento
    if (empty($dados['descricao']) || empty($dados['categoria_id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Preencha os campos obrigatórios (Descrição e Categoria).']);
        exit;
    }
    
    $repo = new LancamentoRepository();
    if ($repo->atualizarPai(AuthHelper::getInstituicaoId(), $lancamentoId, $dados)) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Lançamento atualizado com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao atualizar lançamento.']);
    }
} else {
    // Modo Inserção: Cria Pai e N Filhos parcelados
    if (empty($dados['descricao']) || empty($dados['categoria_id']) || empty($dados['valor']) || empty($dados['data_vencimento'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Preencha os campos obrigatórios (Descrição, Categoria, Valor e Vencimento).']);
        exit;
    }

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
