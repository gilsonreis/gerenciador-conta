<?php
require_once __DIR__ . '/../../config/Database.php';

class RelatorioRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function fluxoCaixaDetalhado(int $instituicaoId, array $filtros) {
        $params = [];
        $whereFilters = [];

        // Filtro de Data
        if (!empty($filtros['data_inicio'])) {
            $whereFilters[] = "data_movimento >= :data_inicio";
            $params['data_inicio'] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $whereFilters[] = "data_movimento <= :data_fim";
            $params['data_fim'] = $filtros['data_fim'];
        }

        // Filtro de Conta
        if (!empty($filtros['conta_id'])) {
            $whereFilters[] = "conta_id = :conta_id";
            $params['conta_id'] = $filtros['conta_id'];
        }

        // Filtro de Tipo (Entrada/Saída) - Aplicado no UNION se possível ou no wrapper
        $subQueryEntradas = "
            SELECT 
                ce.data_entrada as data_movimento, 
                ce.descricao, 
                'Entrada' as categoria_nome, 
                co.nome as conta_nome, 
                'Entrada' as tipo, 
                ce.valor,
                ce.conta_id
            FROM caixa_entradas ce
            JOIN contas co ON ce.conta_id = co.id
            WHERE ce.instituicao_id = :inst1
        ";

        $subQuerySaidas = "
            SELECT 
                p.data_pagamento as data_movimento, 
                l.descricao, 
                cat.nome as categoria_nome, 
                co.nome as conta_nome, 
                'Saída' as tipo, 
                (p.valor - p.desconto) as valor,
                p.conta_pagamento_id as conta_id
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            JOIN categorias cat ON l.categoria_id = cat.id
            JOIN contas co ON p.conta_pagamento_id = co.id
            WHERE l.instituicao_id = :inst2 AND p.data_pagamento IS NOT NULL
        ";

        $unionQuery = "";
        if (empty($filtros['tipo_movimento']) || $filtros['tipo_movimento'] === 'todos') {
            $unionQuery = "($subQueryEntradas) UNION ALL ($subQuerySaidas)";
            $params['inst1'] = $instituicaoId;
            $params['inst2'] = $instituicaoId;
        } elseif ($filtros['tipo_movimento'] === 'entrada') {
            $unionQuery = "$subQueryEntradas";
            $params['inst1'] = $instituicaoId;
        } elseif ($filtros['tipo_movimento'] === 'saida') {
            $unionQuery = "$subQuerySaidas";
            $params['inst2'] = $instituicaoId;
        }

        $whereSql = !empty($whereFilters) ? "WHERE " . implode(" AND ", $whereFilters) : "";

        $finalSql = "
            SELECT * FROM (
                $unionQuery
            ) as fluxo
            $whereSql
            ORDER BY data_movimento ASC
        ";

        $stmt = $this->db->prepare($finalSql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
