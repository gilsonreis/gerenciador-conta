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
     * Retorna 0 para super_admin (sem filtro de instituição = vê tudo).
     * Os repositórios tratam inst=0 como "sem filtro".
     */
    public static function getInstituicaoId(): int {
        if (self::getRole() === 'super_admin') return 0;
        return $_SESSION['instituicao_id'] ?? 0;
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
        return $_SESSION['usuario_role'] ?? 'reader';
    }

    public static function isSuperAdmin(): bool {
        return self::getRole() === 'super_admin';
    }
}
