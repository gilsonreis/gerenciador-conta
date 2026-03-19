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
            " . ($instituicaoId === 0 ? '' : 'WHERE ce.instituicao_id = :inst1');

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
            " . ($instituicaoId === 0 ? 'WHERE p.data_pagamento IS NOT NULL' : 'WHERE l.instituicao_id = :inst2 AND p.data_pagamento IS NOT NULL');

        $unionQuery = "";
        if (empty($filtros['tipo_movimento']) || $filtros['tipo_movimento'] === 'todos') {
            $unionQuery = "($subQueryEntradas) UNION ALL ($subQuerySaidas)";
            if ($instituicaoId !== 0) { $params['inst1'] = $instituicaoId; $params['inst2'] = $instituicaoId; }
        } elseif ($filtros['tipo_movimento'] === 'entrada') {
            $unionQuery = "$subQueryEntradas";
            if ($instituicaoId !== 0) { $params['inst1'] = $instituicaoId; }
        } elseif ($filtros['tipo_movimento'] === 'saida') {
            $unionQuery = "$subQuerySaidas";
            if ($instituicaoId !== 0) { $params['inst2'] = $instituicaoId; }
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
    public function despesasPorCategoria(int $instituicaoId, int $mes, int $ano, string $status = 'todas') {
        $instWhere = $instituicaoId === 0 ? '' : 'AND l.instituicao_id = :inst';
        $params = ['mes_ano' => sprintf('%04d-%02d', $ano, $mes)];
        if ($instituicaoId !== 0) $params['inst'] = $instituicaoId;

        $whereStatus = "";
        if ($status === 'pagas') {
            $whereStatus = "AND p.data_pagamento IS NOT NULL";
        } elseif ($status === 'pendentes') {
            $whereStatus = "AND p.data_pagamento IS NULL";
        }

        $sql = "
            SELECT 
                c.nome as categoria_nome,
                SUM(p.valor) as total
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            JOIN categorias c ON l.categoria_id = c.id
            WHERE DATE_FORMAT(p.data_vencimento, '%Y-%m') = :mes_ano
            $instWhere
            $whereStatus
            GROUP BY c.id
            ORDER BY total DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function contasAPagar(int $instituicaoId, array $filtros) {
        $instWhere = $instituicaoId === 0 ? '' : 'AND l.instituicao_id = :inst';
        $params = [];
        if ($instituicaoId !== 0) $params['inst'] = $instituicaoId;
        $whereFilters = [];

        $status = $filtros['status_vencimento'] ?? 'todos';
        $mes = (int)($filtros['mes'] ?? date('m'));
        $ano = (int)($filtros['ano'] ?? date('Y'));

        if ($status === 'atrasados') {
            $whereFilters[] = "p.data_vencimento < CURDATE()";
        } elseif ($status === 'mes') {
            $whereFilters[] = "MONTH(p.data_vencimento) = :mes AND YEAR(p.data_vencimento) = :ano";
            $params['mes'] = $mes;
            $params['ano'] = $ano;
        } elseif ($status === 'futuro') {
            $whereFilters[] = "p.data_vencimento > LAST_DAY(CURDATE())";
        }

        $whereSql = !empty($whereFilters) ? "AND " . implode(" AND ", $whereFilters) : "";

        $sql = "
            SELECT 
                p.*,
                l.descricao,
                cat.nome as categoria_nome,
                u.nome as usuario_nome
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            JOIN categorias cat ON l.categoria_id = cat.id
            JOIN usuarios u ON l.usuario_id = u.id
            WHERE p.data_pagamento IS NULL
            $instWhere
            $whereSql
            ORDER BY p.data_vencimento ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function resumoConsolidado(int $instituicaoId, array $filtros) {
        $agrupamento = $filtros['agrupamento'] ?? 'mensal';
        $formato = ($agrupamento === 'anual') ? '%Y' : '%m/%Y';

        $instWhere1 = $instituicaoId === 0 ? '' : 'AND instituicao_id = :inst1';
        $instWhere2 = $instituicaoId === 0 ? '' : 'AND l.instituicao_id = :inst2';

        $params = [
            'inicio1' => $filtros['data_inicio'],
            'fim1'    => $filtros['data_fim'],
            'inicio2' => $filtros['data_inicio'],
            'fim2'    => $filtros['data_fim']
        ];
        if ($instituicaoId !== 0) {
            $params['inst1'] = $instituicaoId;
            $params['inst2'] = $instituicaoId;
        }

        $sql = "
            SELECT 
                periodo, 
                SUM(entrada) as total_entradas, 
                SUM(saida) as total_saidas, 
                (SUM(entrada) - SUM(saida)) as saldo_periodo 
            FROM (
                SELECT 
                    DATE_FORMAT(data_entrada, '$formato') as periodo, 
                    valor as entrada, 
                    0 as saida 
                FROM caixa_entradas 
                WHERE data_entrada BETWEEN :inicio1 AND :fim1
                $instWhere1
                
                UNION ALL
                
                SELECT 
                    DATE_FORMAT(data_pagamento, '$formato') as periodo, 
                    0 as entrada, 
                    (valor - desconto) as valor 
                FROM parcelas p
                JOIN lancamentos l ON p.lancamento_id = l.id
                WHERE data_pagamento BETWEEN :inicio2 AND :fim2 
                $instWhere2
                AND data_pagamento IS NOT NULL
            ) as consolidador 
            GROUP BY periodo 
            ORDER BY STR_TO_DATE(CONCAT('01/', periodo), '%d/%m/%Y') ASC, periodo ASC
        ";

        // Ajuste na ordenação se for anual
        if ($agrupamento === 'anual') {
            $sql = str_replace("ORDER BY STR_TO_DATE(CONCAT('01/', periodo), '%d/%m/%Y') ASC, periodo ASC", "ORDER BY periodo ASC", $sql);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
