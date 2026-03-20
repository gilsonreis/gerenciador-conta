<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../src/Helpers/AuthHelper.php';
require_once __DIR__ . '/../../src/Repositories/CategoriaRepository.php';

AuthHelper::requireLogin();

$instId       = AuthHelper::getInstituicaoId();
$isSuperAdmin = $instId === 0;

if ($isSuperAdmin) {
    $filtroInst = filter_input(INPUT_GET, 'filtro_instituicao_id', FILTER_VALIDATE_INT) ?: 0;
    $db         = Database::getConnection();
    $where      = $filtroInst ? 'WHERE c.instituicao_id = :inst' : '';
    $params     = $filtroInst ? ['inst' => $filtroInst] : [];
    $stmt       = $db->prepare("
        SELECT c.id, c.nome, i.nome as instituicao_nome
        FROM categorias c
        LEFT JOIN instituicoes i ON c.instituicao_id = i.id
        $where
        ORDER BY i.nome ASC, c.nome ASC
    ");
    $stmt->execute($params);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $repo       = new CategoriaRepository();
    $categorias = $repo->listar($instId);
}

echo json_encode([
    'sucesso'        => true,
    'dados'          => $categorias,
    'is_super_admin' => $isSuperAdmin,
]);
