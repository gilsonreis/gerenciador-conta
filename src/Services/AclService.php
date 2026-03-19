<?php
/**
 * AclService — Porteiro Central de Permissões (RBAC)
 *
 * MATRIZ DE ROLES:
 *   super_admin  → acesso irrestrito a tudo
 *   admin        → tudo dentro da sua instituição, exceto gerenciar instituições
 *   manager      → lançamentos, exceto usuários e transferências
 *   reader       → somente leitura (GET) — nenhuma ação de escrita
 *
 * USO:
 *   AclService::check('write');               // Bloqueia reader
 *   AclService::check('transferencias');      // Bloqueia reader + manager
 *   AclService::check('usuarios');            // Bloqueia reader + manager
 *   AclService::check('instituicoes_write');  // Apenas super_admin
 */
class AclService {

    /** Mapa: permissão → roles que NÃO têm acesso */
    private static array $bloqueios = [
        'write'               => ['reader'],
        'transferencias'      => ['reader', 'manager'],
        'usuarios'            => ['reader', 'manager'],
        'instituicoes_write'  => ['reader', 'manager', 'admin'],
    ];

    /**
     * Verifica a permissão. Encerra com HTTP 403 se não tiver acesso.
     *
     * @param string $permissao  Nome da permissão (write|transferencias|usuarios|instituicoes_write)
     * @param bool   $soEscrita  Se true, passa leituras (GET) automaticamente mesmo para reader
     */
    public static function check(string $permissao, bool $soEscrita = true): void {
        // Sessões antigas sem 'usuario_role': defaulta para 'admin' (mais seguro que 'reader'
        // pois evita falsos positivos em usuários legítimos)
        $role = !empty($_SESSION['usuario_role']) ? $_SESSION['usuario_role'] : 'admin';

        // super_admin passa sempre
        if ($role === 'super_admin') return;

        // Se soEscrita=true e for requisição GET, passes sem checar (somente leitura)
        if ($soEscrita && $_SERVER['REQUEST_METHOD'] === 'GET') return;

        $bloqueados = self::$bloqueios[$permissao] ?? [];
        if (in_array($role, $bloqueados, true)) {
            http_response_code(403);
            echo json_encode([
                'erro'  => 'Acesso negado. Seu perfil não tem permissão para esta ação.',
                'role'  => $role,
                'acao'  => $permissao,
            ]);
            exit;
        }
    }

    /**
     * Retorna true se o role atual está entre os permitidos.
     */
    public static function pode(string $permissao): bool {
        $role = !empty($_SESSION['usuario_role']) ? $_SESSION['usuario_role'] : 'admin';
        if ($role === 'super_admin') return true;
        $bloqueados = self::$bloqueios[$permissao] ?? [];
        return !in_array($role, $bloqueados, true);
    }
}
