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
                id, 
                origem, 
                valor, 
                data_entrada
            FROM caixa_entradas
            WHERE instituicao_id = :instituicao_id
            AND DATE_FORMAT(data_entrada, '%Y-%m') = :mes_ano
            ORDER BY data_entrada ASC
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
            $sql = "UPDATE caixa_entradas SET origem = :origem, valor = :valor, data_entrada = :data_entrada WHERE id = :id AND instituicao_id = :instituicao_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'origem' => $dados['origem'],
                'valor' => $valor,
                'data_entrada' => $dados['data_entrada'],
                'id' => $dados['id'],
                'instituicao_id' => $instituicaoId
            ]);
        } else {
            $sql = "INSERT INTO caixa_entradas (instituicao_id, usuario_id, origem, valor, data_entrada) VALUES (:instituicao_id, :usuario_id, :origem, :valor, :data_entrada)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'instituicao_id' => $instituicaoId,
                'usuario_id' => $usuarioId,
                'origem' => $dados['origem'],
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
