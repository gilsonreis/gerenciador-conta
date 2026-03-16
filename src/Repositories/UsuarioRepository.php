<?php
require_once __DIR__ . '/../../config/Database.php';

class UsuarioRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listar(int $instituicaoId) {
        $sql = "SELECT u.id, u.nome, u.email, i.nome as instituicao_nome 
                FROM usuarios u 
                LEFT JOIN instituicoes i ON u.instituicao_id = i.id 
                WHERE u.instituicao_id = :instituicao_id 
                ORDER BY u.nome ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instituicao_id' => $instituicaoId]);
        return $stmt->fetchAll();
    }

    public function buscar(int $instituicaoId, int $id) {
        $sql = "SELECT id, instituicao_id, nome, email FROM usuarios WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
        return $stmt->fetch();
    }

    public function salvar(int $instituicaoId, array $dados) {
        // Se a instituição foi enviada no form, usa a nova; se não, mantém a atual de quem está logado
        $instituicaoAlvo = !empty($dados['instituicao_id']) ? $dados['instituicao_id'] : $instituicaoId;

        if (!empty($dados['id'])) {
            $sql = "UPDATE usuarios SET instituicao_id = :instituicao_alvo, nome = :nome, email = :email WHERE id = :id AND instituicao_id = :instituicao_id";
            $params = [
                'nome' => $dados['nome'], 
                'email' => $dados['email'], 
                'id' => $dados['id'], 
                'instituicao_alvo' => $instituicaoAlvo,
                'instituicao_id' => $instituicaoId
            ];
            
            if (!empty($dados['senha'])) {
                $sql = "UPDATE usuarios SET instituicao_id = :instituicao_alvo, nome = :nome, email = :email, senha = :senha WHERE id = :id AND instituicao_id = :instituicao_id";
                $params['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            }
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } else {
            $sql = "INSERT INTO usuarios (instituicao_id, nome, email, senha) VALUES (:instituicao_alvo, :nome, :email, :senha)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'instituicao_alvo' => $instituicaoAlvo, 
                'nome' => $dados['nome'], 
                'email' => $dados['email'],
                'senha' => password_hash($dados['senha'] ?? '123456', PASSWORD_DEFAULT)
            ]);
        }
    }

    public function excluir(int $instituicaoId, int $id) {
        $sql = "DELETE FROM usuarios WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
    }

    public function autenticar(string $email, string $senha) {
        $sql = "SELECT id, instituicao_id, nome, senha FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            unset($usuario['senha']);
            return $usuario;
        }

        return false;
    }
}
