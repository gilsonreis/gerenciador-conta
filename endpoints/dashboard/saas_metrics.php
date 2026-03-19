<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';

AuthHelper::requireLogin();

// Exclusivo: super_admin
if (AuthHelper::getInstituicaoId() !== 0) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso negado.']);
    exit;
}

$db = Database::getConnection();

try {
    // 1. Total de Instituições
    $totalInstituicoes = (int)$db->query("SELECT COUNT(id) FROM instituicoes")->fetchColumn();

    // 2. Total de Usuários (excluindo super_admins — coluna role pode ainda não ter migration rodada)
    $totalUsuarios = (int)$db->query("
        SELECT COUNT(id) FROM usuarios
        WHERE COALESCE(role, 'admin') != 'super_admin'
    ")->fetchColumn();

    // 3. Volume de Lançamentos (parcelas)
    $totalLancamentos = (int)$db->query("SELECT COUNT(id) FROM parcelas")->fetchColumn();

    // 4. Últimos 5 usuários cadastrados (por id DESC, sem super_admins)
    $stmtRecentes = $db->prepare("
        SELECT
            u.id,
            u.nome,
            u.email,
            COALESCE(u.role, 'admin') as role,
            COALESCE(i.nome, '—') as instituicao_nome
        FROM usuarios u
        LEFT JOIN instituicoes i ON u.instituicao_id = i.id
        WHERE COALESCE(u.role, 'admin') != 'super_admin'
        ORDER BY u.id DESC
        LIMIT 5
    ");
    $stmtRecentes->execute();
    $usuariosRecentes = $stmtRecentes->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'sucesso' => true,
        'metricas' => [
            'total_instituicoes'         => $totalInstituicoes,
            'total_usuarios'             => $totalUsuarios,
            'total_lancamentos'          => $totalLancamentos,
            'total_lancamentos_formatado'=> number_format($totalLancamentos, 0, ',', '.'),
        ],
        'usuarios_recentes' => $usuariosRecentes,
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao carregar métricas.', 'detalhe' => $e->getMessage()]);
}
