<?php
class AuthHelper {
    public static function requireLogin(): void {
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['erro' => 'Sessão expirada ou não autenticada.', 'redirect' => 'login.php']);
            exit;
        }
    }

    public static function getInstituicaoId(): int {
        return $_SESSION['instituicao_id'] ?? 0;
    }

    public static function getUsuarioId(): int {
        return $_SESSION['usuario_id'] ?? 0;
    }
}
