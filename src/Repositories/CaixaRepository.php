<?php
require_once __DIR__ . '/../../config/Database.php';

class CaixaRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarPorMes(int $instituicaoId, string $mesAno) {
        $sql = "
            SELECT 
                ce.id, 
                ce.conta_id,
                c.nome as conta_nome,
                ce.valor, 
                ce.data_entrada
            FROM caixa_entradas ce
            JOIN contas c ON ce.conta_id = c.id
            WHERE ce.instituicao_id = :instituicao_id
            AND DATE_FORMAT(ce.data_entrada, '%Y-%m') = :mes_ano
            ORDER BY ce.data_entrada ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instituicao_id' => $instituicaoId, 'mes_ano' => $mesAno]);
        return $stmt->fetchAll();
    }

    public function resumoMes(int $instituicaoId, string $mesAno) {
        $sql = "
            SELECT SUM(valor) as total_entradas
            FROM caixa_entradas
            WHERE instituicao_id = :instituicao_id
            AND DATE_FORMAT(data_entrada, '%Y-%m') = :mes_ano
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instituicao_id' => $instituicaoId, 'mes_ano' => $mesAno]);
        return $stmt->fetch()['total_entradas'] ?? 0;
    }

    public function buscar(int $instituicaoId, int $id) {
        $sql = "SELECT * FROM caixa_entradas WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
        return $stmt->fetch();
    }

    public function salvar(int $instituicaoId, int $usuarioId, array $dados) {
        $valor = (float)str_replace(',', '.', str_replace('.', '', $dados['valor']));
        
        if (!empty($dados['id'])) {
            $sql = "UPDATE caixa_entradas SET conta_id = :conta_id, valor = :valor, data_entrada = :data_entrada WHERE id = :id AND instituicao_id = :instituicao_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'conta_id' => $dados['conta_id'],
                'valor' => $valor,
                'data_entrada' => $dados['data_entrada'],
                'id' => $dados['id'],
                'instituicao_id' => $instituicaoId
            ]);
        } else {
            $sql = "INSERT INTO caixa_entradas (instituicao_id, usuario_id, conta_id, valor, data_entrada) VALUES (:instituicao_id, :usuario_id, :conta_id, :valor, :data_entrada)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'instituicao_id' => $instituicaoId,
                'usuario_id' => $usuarioId,
                'conta_id' => $dados['conta_id'],
                'valor' => $valor,
                'data_entrada' => $dados['data_entrada']
            ]);
        }
    }

    public function excluir(int $instituicaoId, int $id) {
        $sql = "DELETE FROM caixa_entradas WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
    }
}
