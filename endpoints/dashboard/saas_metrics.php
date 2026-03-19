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

// 1. Total de Instituições
$totalInstituicoes = (int)$db->query("SELECT COUNT(id) FROM instituicoes")->fetchColumn();

// 2. Total de Usuários (excluindo super_admins)
$totalUsuarios = (int)$db->query("SELECT COUNT(id) FROM usuarios WHERE role != 'super_admin'")->fetchColumn();

// 3. Volume de Lançamentos (parcelas — principal tabela de volume)
$totalLancamentos = (int)$db->query("SELECT COUNT(id) FROM parcelas")->fetchColumn();

// 4. Últimos 5 usuários cadastrados (sem super_admins, com instituição)
$stmtRecentes = $db->prepare("
    SELECT 
        u.nome,
        u.email,
        u.role,
        u.criado_em,
        COALESCE(i.nome, '—') as instituicao_nome
    FROM usuarios u
    LEFT JOIN instituicoes i ON u.instituicao_id = i.id
    WHERE u.role != 'super_admin'
    ORDER BY u.criado_em DESC
    LIMIT 5
");
$stmtRecentes->execute();
$usuariosRecentes = $stmtRecentes->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'sucesso' => true,
    'metricas' => [
        'total_instituicoes' => $totalInstituicoes,
        'total_usuarios'     => $totalUsuarios,
        'total_lancamentos'  => $totalLancamentos,
        'total_lancamentos_formatado' => number_format($totalLancamentos, 0, ',', '.'),
    ],
    'usuarios_recentes' => $usuariosRecentes,
]);
