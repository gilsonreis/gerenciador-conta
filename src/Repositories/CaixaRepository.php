<?php
require_once __DIR__ . '/../../config/Database.php';

class CaixaRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarPorMes(int $instituicaoId, string $mesAno) {
        $instWhere = $instituicaoId === 0 ? '' : 'AND ce.instituicao_id = :instituicao_id';
        $sql = "
            SELECT 
                ce.id, 
                ce.conta_id,
                c.nome as conta_nome,
                ce.descricao,
                ce.valor, 
                ce.data_entrada
            FROM caixa_entradas ce
            JOIN contas c ON ce.conta_id = c.id
            WHERE DATE_FORMAT(ce.data_entrada, '%Y-%m') = :mes_ano
            $instWhere
            ORDER BY ce.data_entrada ASC
        ";
        $params = ['mes_ano' => $mesAno];
        if ($instituicaoId !== 0) $params['instituicao_id'] = $instituicaoId;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function resumoMes(int $instituicaoId, string $mesAno) {
        $instWhere = $instituicaoId === 0 ? '' : 'AND instituicao_id = :instituicao_id';
        $sql = "
            SELECT SUM(valor) as total_entradas
            FROM caixa_entradas
            WHERE DATE_FORMAT(data_entrada, '%Y-%m') = :mes_ano
            $instWhere
        ";
        $params = ['mes_ano' => $mesAno];
        if ($instituicaoId !== 0) $params['instituicao_id'] = $instituicaoId;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float)($stmt->fetch()['total_entradas'] ?? 0);
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
            $sql = "UPDATE caixa_entradas SET conta_id = :conta_id, descricao = :descricao, valor = :valor, data_entrada = :data_entrada WHERE id = :id AND instituicao_id = :instituicao_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'conta_id' => $dados['conta_id'],
                'descricao' => $dados['descricao'] ?? null,
                'valor' => $valor,
                'data_entrada' => $dados['data_entrada'],
                'id' => $dados['id'],
                'instituicao_id' => $instituicaoId
            ]);
        } else {
            $sql = "INSERT INTO caixa_entradas (instituicao_id, usuario_id, conta_id, descricao, valor, data_entrada) VALUES (:instituicao_id, :usuario_id, :conta_id, :descricao, :valor, :data_entrada)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'instituicao_id' => $instituicaoId,
                'usuario_id' => $usuarioId,
                'conta_id' => $dados['conta_id'],
                'descricao' => $dados['descricao'] ?? null,
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
