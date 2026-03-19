<?php
require_once __DIR__ . '/../../config/Database.php';

class CategoriaRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listar(int $instituicaoId) {
        $sql = "SELECT * FROM categorias WHERE (:instituicao_id = 0 OR instituicao_id = :instituicao_id) ORDER BY nome ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instituicao_id' => $instituicaoId]);
        return $stmt->fetchAll();
    }

    public function buscar(int $instituicaoId, int $id) {
        $sql = "SELECT id, nome FROM categorias WHERE id = :id AND (:instituicao_id = 0 OR instituicao_id = :instituicao_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
        return $stmt->fetch();
    }

    public function salvar(int $instituicaoId, array $dados) {
        if (!empty($dados['id'])) {
            $sql = "UPDATE categorias SET nome = :nome WHERE id = :id AND instituicao_id = :instituicao_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['nome' => $dados['nome'], 'id' => $dados['id'], 'instituicao_id' => $instituicaoId]);
        } else {
            $sql = "INSERT INTO categorias (instituicao_id, nome) VALUES (:instituicao_id, :nome)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['instituicao_id' => $instituicaoId, 'nome' => $dados['nome']]);
        }
    }

    public function excluir(int $instituicaoId, int $id) {
        $sql = "DELETE FROM categorias WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
    }
}
