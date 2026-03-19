<?php
require_once __DIR__ . '/../../config/Database.php';

class ContaRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listar(int $instituicaoId) {
        if ($instituicaoId === 0) {
            $sql = "SELECT * FROM contas ORDER BY nome ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } else {
            $sql = "SELECT * FROM contas WHERE instituicao_id = :instituicao_id ORDER BY nome ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['instituicao_id' => $instituicaoId]);
        }
        return $stmt->fetchAll();
    }

    public function buscar(int $instituicaoId, int $id) {
        if ($instituicaoId === 0) {
            $sql = "SELECT id, nome, saldo_inicial FROM contas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
        } else {
            $sql = "SELECT id, nome, saldo_inicial FROM contas WHERE id = :id AND instituicao_id = :instituicao_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
        }
        return $stmt->fetch();
    }

    public function salvar(int $instituicaoId, array $dados) {
        $saldo_inicial = isset($dados['saldo_inicial']) ? (float)str_replace(',', '.', str_replace('.', '', $dados['saldo_inicial'])) : 0.00;
        
        if (!empty($dados['id'])) {
            $sql = "UPDATE contas SET nome = :nome, saldo_inicial = :saldo WHERE id = :id AND instituicao_id = :instituicao_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'nome' => $dados['nome'],
                'saldo' => $saldo_inicial,
                'id' => $dados['id'],
                'instituicao_id' => $instituicaoId
            ]);
        } else {
            $sql = "INSERT INTO contas (instituicao_id, nome, saldo_inicial) VALUES (:instituicao_id, :nome, :saldo)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'instituicao_id' => $instituicaoId,
                'nome' => $dados['nome'],
                'saldo' => $saldo_inicial
            ]);
        }
    }

    /**
     * @deprecated Usa soma histórica acumulada desde o início dos tempos.
     *             Não use em endpoints de listagem ou validações.
     *             Cada consumidor deve usar: BalanceService::getSaldosInstituicao()
     *
     *             Este método é mantido exclusivamente para o cron de snapshots
     *             (cron/salvar_snapshots.php), onde o saldo acumulado real é necessário.
     */
    public function saldos(int $instituicaoId) {
        $instWhere = $instituicaoId === 0 ? '' : 'WHERE c.instituicao_id = :instituicao_id';
        $sql = "
            SELECT 
                c.id,
                c.nome,
                c.saldo_inicial,
                COALESCE((SELECT SUM(valor) FROM caixa_entradas WHERE conta_id = c.id), 0) as total_entradas,
                COALESCE((SELECT SUM(valor - desconto) FROM parcelas WHERE conta_pagamento_id = c.id AND data_pagamento IS NOT NULL), 0) as total_saidas,
                COALESCE((SELECT SUM(valor) FROM transferencias WHERE conta_destino_id = c.id), 0) as total_transf_entrada,
                COALESCE((SELECT SUM(valor) FROM transferencias WHERE conta_origem_id = c.id), 0) as total_transf_saida,
                (c.saldo_inicial 
                 + COALESCE((SELECT SUM(valor) FROM caixa_entradas WHERE conta_id = c.id), 0) 
                 - COALESCE((SELECT SUM(valor - desconto) FROM parcelas WHERE conta_pagamento_id = c.id AND data_pagamento IS NOT NULL), 0)
                 + COALESCE((SELECT SUM(valor) FROM transferencias WHERE conta_destino_id = c.id), 0)
                 - COALESCE((SELECT SUM(valor) FROM transferencias WHERE conta_origem_id = c.id), 0)
                ) as saldo_atual_real
            FROM contas c
            $instWhere
        ";
        $stmt = $this->db->prepare($sql);
        $params = $instituicaoId === 0 ? [] : ['instituicao_id' => $instituicaoId];
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function excluir(int $instituicaoId, int $id) {
        $sql = "DELETE FROM contas WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
    }
}
