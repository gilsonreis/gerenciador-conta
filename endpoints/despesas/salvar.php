<?php
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/LancamentoRepository.php';
require_once __DIR__ . '/../../src/Repositories/ParcelaRepository.php';
require_once __DIR__ . '/../../src/Services/AclService.php';

AuthHelper::requireLogin();
AclService::check('write');

$lancamentoId = filter_input(INPUT_POST, 'lancamento_id', FILTER_VALIDATE_INT);
$dados = $_POST;

// ── Determinação segura do dono do registro ───────────────────────────
// Super admin pode escolher a qual instituição/usuário o lançamento pertence.
// Demais perfis: sempre usa os valores da sessão (blindagem de segurança).
$isSuperAdmin = AuthHelper::getInstituicaoId() === 0;

if ($isSuperAdmin) {
    $instId   = filter_input(INPUT_POST, 'instituicao_id', FILTER_VALIDATE_INT) ?: 0;
    $userId   = filter_input(INPUT_POST, 'usuario_id',   FILTER_VALIDATE_INT) ?: AuthHelper::getUsuarioId();
    if (empty($instId)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Selecione uma Instituição para esta despesa.']);
        exit;
    }
} else {
    $instId = AuthHelper::getInstituicaoIdReal();
    $userId = AuthHelper::getUsuarioId();
}
// ──────────────────────────────────────────────────────────────────────

if ($lancamentoId) {
    // Modo Edição: Atualiza Apenas os dados PAI do Lançamento
    if (empty($dados['descricao']) || empty($dados['categoria_id'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Preencha os campos obrigatórios (Descrição e Categoria).']);
        exit;
    }

    $repo = new LancamentoRepository();
    if ($repo->atualizarPai($instId, $lancamentoId, $dados)) {
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
        if ($repo->salvarComParcelas($instId, $userId, $dados)) {
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
