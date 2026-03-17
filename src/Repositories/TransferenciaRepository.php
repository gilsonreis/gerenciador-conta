<?php
require_once __DIR__ . '/../../config/Database.php';

class TransferenciaRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function salvar(int $instituicaoId, array $dados) {
        $valor = (float)str_replace(',', '.', str_replace('.', '', $dados['valor']));
        
        $sql = "INSERT INTO transferencias 
                (instituicao_id, conta_origem_id, conta_destino_id, valor, data_transferencia, descricao) 
                VALUES 
                (:inst, :origem, :destino, :valor, :data, :desc)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'inst' => $instituicaoId,
            'origem' => $dados['conta_origem_id'],
            'destino' => $dados['conta_destino_id'],
            'valor' => $valor,
            'data' => $dados['data_transferencia'],
            'desc' => !empty($dados['descricao']) ? $dados['descricao'] : 'Transferência entre contas'
        ]);
    }

    public function listar(int $instituicaoId, int $limit = 50) {
        $sql = "
            SELECT 
                t.*,
                co.nome as conta_origem_nome,
                cd.nome as conta_destino_nome
            FROM transferencias t
            JOIN contas co ON t.conta_origem_id = co.id
            JOIN contas cd ON t.conta_destino_id = cd.id
            WHERE t.instituicao_id = :inst
            ORDER BY t.data_transferencia DESC, t.id DESC
            LIMIT :limit
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':inst', $instituicaoId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function excluir(int $instituicaoId, int $id) {
        $sql = "DELETE FROM transferencias WHERE id = :id AND instituicao_id = :inst";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'inst' => $instituicaoId]);
    }
}
