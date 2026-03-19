<?php
require_once __DIR__ . '/../../config/Database.php';

class InstituicaoRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listar($usuarioId) {
        // super_admin (usuarioId=0): retorna todas as instituições sem filtro de usuário
        if ($usuarioId === 0) {
            $stmt = $this->db->prepare("SELECT id, nome FROM instituicoes ORDER BY nome ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
        $stmt = $this->db->prepare("
            SELECT i.id, i.nome 
            FROM instituicoes i
            JOIN usuarios u ON i.id = u.instituicao_id
            WHERE u.id = ?
            ORDER BY i.nome ASC
        ");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public function buscar($usuarioId, $id) {
        // super_admin: busca sem filtro de usuário
        if ($usuarioId === 0) {
            $stmt = $this->db->prepare("SELECT id, nome FROM instituicoes WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
        $stmt = $this->db->prepare("
            SELECT i.id, i.nome 
            FROM instituicoes i
            JOIN usuarios u ON i.id = u.instituicao_id 
            WHERE i.id = ? AND u.id = ?
        ");
        $stmt->execute([$id, $usuarioId]);
        return $stmt->fetch();
    }

    public function salvar($usuarioId, $dados) {
        // Validation: Verify if the user attempting to update belongs to this institution
        if (!empty($dados['id'])) {
            $check = $this->buscar($usuarioId, $dados['id']);
            if (!$check) {
                return false; // User not allowed to edit this institution
            }
        }

        if (empty($dados['id'])) {
            $stmt = $this->db->prepare("INSERT INTO instituicoes (nome) VALUES (?)");
            return $stmt->execute([$dados['nome']]);
        } else {
            $stmt = $this->db->prepare("UPDATE instituicoes SET nome = ? WHERE id = ?");
            return $stmt->execute([$dados['nome'], $dados['id']]);
        }
    }

    public function excluir($usuarioId, $id) {
         // Validation: Verify if the user attempting to delete belongs to this institution
         $check = $this->buscar($usuarioId, $id);
         if (!$check) {
             return false;
         }

        $stmt = $this->db->prepare("DELETE FROM instituicoes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
