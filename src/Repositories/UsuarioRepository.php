<?php
require_once __DIR__ . '/../../config/Database.php';

class UsuarioRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listar(int $instituicaoId) {
        if ($instituicaoId === 0) {
            $sql = "SELECT u.id, u.nome, u.email, u.role, u.recebe_alertas, i.nome as instituicao_nome 
                    FROM usuarios u 
                    LEFT JOIN instituicoes i ON u.instituicao_id = i.id 
                    ORDER BY u.nome ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } else {
            $sql = "SELECT u.id, u.nome, u.email, u.role, u.recebe_alertas, i.nome as instituicao_nome 
                    FROM usuarios u 
                    LEFT JOIN instituicoes i ON u.instituicao_id = i.id 
                    WHERE u.instituicao_id = :instituicao_id 
                    ORDER BY u.nome ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['instituicao_id' => $instituicaoId]);
        }
        return $stmt->fetchAll();
    }

    public function buscar(int $instituicaoId, int $id) {
        if ($instituicaoId === 0) {
            // super_admin pode buscar qualquer usuário
            $stmt = $this->db->prepare("SELECT id, instituicao_id, nome, email, role, recebe_alertas FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $id]);
        } else {
            $stmt = $this->db->prepare("SELECT id, instituicao_id, nome, email, role, recebe_alertas FROM usuarios WHERE id = :id AND instituicao_id = :instituicao_id");
            $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
        }
        return $stmt->fetch();
    }

    public function salvar(int $instituicaoId, array $dados) {
        // Se a instituição foi enviada no form, usa a nova; se não, usa a do logado
        $instituicaoAlvo = !empty($dados['instituicao_id']) ? (int)$dados['instituicao_id'] : $instituicaoId;

        if (!empty($dados['id'])) {
            // UPDATE: super_admin (inst=0) pode editar qualquer usuário sem filtro de inst
            $instFilter = $instituicaoId === 0 ? '' : 'AND instituicao_id = :instituicao_id';
            $sql = "UPDATE usuarios SET instituicao_id = :instituicao_alvo, nome = :nome, email = :email, recebe_alertas = :recebe_alertas WHERE id = :id $instFilter";
            $params = [
                'nome'            => $dados['nome'],
                'email'           => $dados['email'],
                'id'              => $dados['id'],
                'instituicao_alvo'=> $instituicaoAlvo,
                'recebe_alertas'  => isset($dados['recebe_alertas']) ? 1 : 0
            ];
            if ($instituicaoId !== 0) $params['instituicao_id'] = $instituicaoId;

            if (!empty($dados['role'])) {
                $sql = str_replace('recebe_alertas = :recebe_alertas WHERE', 'recebe_alertas = :recebe_alertas, role = :role WHERE', $sql);
                $params['role'] = $dados['role'];
            }

            if (!empty($dados['senha'])) {
                $sql = str_replace('recebe_alertas = :recebe_alertas', 'recebe_alertas = :recebe_alertas, senha = :senha', $sql);
                $params['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            }

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } else {
            // INSERT
            $role = !empty($dados['role']) ? $dados['role'] : 'admin';
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (instituicao_id, nome, email, senha, recebe_alertas, role)
                VALUES (:instituicao_alvo, :nome, :email, :senha, :recebe_alertas, :role)
            ");
            return $stmt->execute([
                'instituicao_alvo' => $instituicaoAlvo,
                'nome'             => $dados['nome'],
                'email'            => $dados['email'],
                'senha'            => password_hash($dados['senha'] ?? '123456', PASSWORD_DEFAULT),
                'recebe_alertas'   => 1,
                'role'             => $role,
            ]);
        }
    }

    public function excluir(int $instituicaoId, int $id) {
        $sql = "DELETE FROM usuarios WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'instituicao_id' => $instituicaoId]);
    }

    public function autenticar(string $email, string $senha) {
        $sql = "SELECT id, instituicao_id, nome, senha, role FROM usuarios WHERE email = :email";
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
