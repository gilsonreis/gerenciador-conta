<?php
class AuthHelper {
    public static function requireLogin(): void {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['erro' => 'Sessão expirada ou não autenticada.', 'redirect' => 'login.php']);
            exit;
        }
    }

    /**
     * Retorna 0 quando:
     *   - role = 'super_admin' (verificação via role), OU
     *   - instituicao_id da sessão é NULL/0 (super_admin com campo nullable no BD)
     * Repositórios tratam inst=0 como "sem filtro" → acesso total.
     */
    public static function getInstituicaoId(): int {
        // Super admin por role
        if (!empty($_SESSION['usuario_role']) && $_SESSION['usuario_role'] === 'super_admin') return 0;
        // Super admin por NULL no campo (após migration)
        if (!isset($_SESSION['instituicao_id']) || $_SESSION['instituicao_id'] === null) return 0;
        return (int)$_SESSION['instituicao_id'];
    }

    /**
     * Sempre retorna o instituicao_id real da sessão,
     * usado em operações de escrita onde super_admin age dentro da sua própria instituição.
     */
    public static function getInstituicaoIdReal(): int {
        return $_SESSION['instituicao_id'] ?? 0;
    }

    public static function getUsuarioId(): int {
        return $_SESSION['usuario_id'] ?? 0;
    }

    public static function getRole(): string {
        // Usa !empty para tratar null e string vazia como 'admin' (usuários sem migration)
        return !empty($_SESSION['usuario_role']) ? $_SESSION['usuario_role'] : 'admin';
    }

    public static function isSuperAdmin(): bool {
        return self::getRole() === 'super_admin';
    }
}
