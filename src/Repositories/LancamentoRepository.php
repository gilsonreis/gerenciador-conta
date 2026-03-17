<?php
require_once __DIR__ . '/../../config/Database.php';

class LancamentoRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarPorMes(int $instituicaoId, string $mesAno, ?int $categoriaId = null, ?int $contaFixa = null, int $pagina = 1, int $itensPorPagina = 20, string $busca = '') {
        $where = "
            WHERE l.instituicao_id = :instituicao_id
            AND DATE_FORMAT(p.data_vencimento, '%Y-%m') = :mes_ano
        ";
        
        $params = ['instituicao_id' => $instituicaoId, 'mes_ano' => $mesAno];
        
        if ($categoriaId !== null) {
            $where .= " AND l.categoria_id = :cat_id";
            $params['cat_id'] = $categoriaId;
        }
        if ($contaFixa !== null) {
            $where .= " AND l.conta_fixa = :fixa";
            $params['fixa'] = $contaFixa;
        }
        if (!empty($busca)) {
            $where .= " AND l.descricao LIKE :busca";
            $params['busca'] = "%{$busca}%";
        }

        // 1. Query de Contagem Total
        $sqlCount = "SELECT COUNT(*) as total FROM parcelas p JOIN lancamentos l ON p.lancamento_id = l.id " . $where;
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRegistros = (int)$stmtCount->fetchColumn();

        // 2. Query Principal com Paginação
        $offset = ($pagina - 1) * $itensPorPagina;
        $sql = "
            SELECT 
                p.id as parcela_id,
                p.lancamento_id,
                l.descricao,
                c.nome as categoria_nome,
                l.conta_fixa,
                p.numero_parcela,
                p.total_parcelas,
                p.valor,
                p.desconto,
                p.data_vencimento,
                p.data_pagamento,
                (SELECT COUNT(*) FROM parcelas WHERE lancamento_id = l.id) as qtd_total_parcelas,
                (SELECT COUNT(*) FROM parcelas WHERE lancamento_id = l.id AND data_pagamento IS NOT NULL) as qtd_parcelas_pagas
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            JOIN categorias c ON l.categoria_id = c.id
            " . $where . "
            ORDER BY p.data_vencimento ASC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return [
            'dados' => $stmt->fetchAll(),
            'total' => $totalRegistros
        ];
    }

    public function resumoMes(int $instituicaoId, string $mesAno, ?int $categoriaId = null, ?int $contaFixa = null, string $busca = '') {
        $sql = "
            SELECT 
                SUM(CASE WHEN p.data_pagamento IS NOT NULL THEN p.valor - p.desconto ELSE 0 END) as total_saidas,
                SUM(CASE WHEN l.conta_fixa = 1 AND p.data_pagamento IS NOT NULL THEN p.valor - p.desconto ELSE 0 END) as custo_vida
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            WHERE l.instituicao_id = :instituicao_id
            AND DATE_FORMAT(p.data_vencimento, '%Y-%m') = :mes_ano
        ";

        $params = ['instituicao_id' => $instituicaoId, 'mes_ano' => $mesAno];
        
        if ($categoriaId !== null) {
            $sql .= " AND l.categoria_id = :cat_id";
            $params['cat_id'] = $categoriaId;
        }
        if ($contaFixa !== null) {
            $sql .= " AND l.conta_fixa = :fixa";
            $params['fixa'] = $contaFixa;
        }
        if (!empty($busca)) {
            $sql .= " AND l.descricao LIKE :busca";
            $params['busca'] = "%{$busca}%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function salvarComParcelas(int $instituicaoId, int $usuarioId, array $dados) {
        try {
            $this->db->beginTransaction();

            // Insert Lançamento Pai
            $sqlLancamento = "INSERT INTO lancamentos (instituicao_id, usuario_id, categoria_id, descricao, conta_fixa) VALUES (:inst, :user, :cat, :desc, :fixa)";
            $stmtLanc = $this->db->prepare($sqlLancamento);
            $stmtLanc->execute([
                'inst' => $instituicaoId,
                'user' => $usuarioId,
                'cat' => $dados['categoria_id'],
                'desc' => $dados['descricao'],
                'fixa' => isset($dados['conta_fixa']) && $dados['conta_fixa'] ? 1 : 0
            ]);

            $lancamentoId = $this->db->lastInsertId();

            // Tratamento de Parcelas
            $totalParcelas = (int)($dados['total_parcelas'] ?? 1);
            $parcelaInicial = (int)($dados['parcela_inicial'] ?? 1);
            $valorParcela = (float)str_replace(',', '.', str_replace('.', '', $dados['valor']));
            
            $dataBase = new DateTime($dados['data_vencimento']);
            $status = $dados['status'] ?? 'pendente';
            $contaPagamentoId = !empty($dados['conta_pagamento_id']) ? $dados['conta_pagamento_id'] : null;

            $sqlParcela = "
                INSERT INTO parcelas 
                (lancamento_id, numero_parcela, total_parcelas, valor, data_vencimento, data_pagamento, conta_pagamento_id) 
                VALUES 
                (:lanc, :num, :total, :val, :venc, :pgto, :conta)
            ";
            $stmtParc = $this->db->prepare($sqlParcela);

            for ($i = $parcelaInicial; $i <= $totalParcelas; $i++) {
                $vencimento = $dataBase->format('Y-m-d');
                
                // Regra: Apenas a primeira parcela gerada pode ser quitada no ato da criação
                $isPrimeira = ($i == $parcelaInicial);
                $dataPagamento = ($status === 'pago' && $isPrimeira) ? $vencimento : null;
                $contaIdFinal = ($status === 'pago' && $isPrimeira) ? $contaPagamentoId : null;

                $stmtParc->execute([
                    'lanc' => $lancamentoId,
                    'num' => $i,
                    'total' => $totalParcelas,
                    'val' => $valorParcela,
                    'venc' => $vencimento,
                    'pgto' => $dataPagamento,
                    'conta' => $contaIdFinal
                ]);

                // Incrementa 1 mes pra proxima iteração
                $dataBase->modify('+1 month');
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function buscar(int $instituicaoId, int $parcelaId) {
        $sql = "
            SELECT 
                p.id as parcela_id,
                l.id as lancamento_id,
                l.descricao,
                l.categoria_id,
                l.conta_fixa,
                p.numero_parcela,
                p.total_parcelas,
                p.valor,
                p.desconto,
                p.data_vencimento,
                p.data_pagamento
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            WHERE l.instituicao_id = :instituicao_id AND p.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instituicao_id' => $instituicaoId, 'id' => $parcelaId]);
        return $stmt->fetch();
    }

    public function buscarLancamentoEParcelas(int $instituicaoId, int $lancamentoId) {
        // Busca Lancamento Pai
        $sqlLanc = "
            SELECT l.id, l.descricao, l.categoria_id, c.nome as categoria_nome, l.conta_fixa
            FROM lancamentos l
            JOIN categorias c ON l.categoria_id = c.id
            WHERE l.id = :id AND l.instituicao_id = :inst
        ";
        $stmtLanc = $this->db->prepare($sqlLanc);
        $stmtLanc->execute(['id' => $lancamentoId, 'inst' => $instituicaoId]);
        $lancamento = $stmtLanc->fetch();

        if (!$lancamento) return false;

        // Busca Parcelas Filhas
        $sqlParc = "
            SELECT id, numero_parcela, total_parcelas, valor, desconto, data_vencimento, data_pagamento
            FROM parcelas
            WHERE lancamento_id = :id
            ORDER BY data_vencimento ASC
        ";
        $stmtParc = $this->db->prepare($sqlParc);
        $stmtParc->execute(['id' => $lancamentoId]);
        $parcelas = $stmtParc->fetchAll();

        return ['lancamento' => $lancamento, 'parcelas' => $parcelas];
    }

    public function listarParcelasPai(int $lancamentoId, string $busca = '', int $pagina = 1, int $itensPorPagina = 12) {
        $where = "WHERE lancamento_id = :id";
        $params = ['id' => $lancamentoId];

        if (!empty($busca)) {
            // Se for numero, busca exata. Se não, ignoramos por enquanto já que a tabela parcelas não tem descricao propria
            if (is_numeric($busca)) {
                $where .= " AND numero_parcela = :busca_num";
                $params['busca_num'] = (int)$busca;
            }
        }

        // 1. Contagem Total
        $sqlCount = "SELECT COUNT(*) FROM parcelas " . $where;
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRegistros = (int)$stmtCount->fetchColumn();

        // 2. Busca Paginada
        $offset = ($pagina - 1) * $itensPorPagina;
        $sql = "
            SELECT id, numero_parcela, total_parcelas, valor, desconto, data_vencimento, data_pagamento
            FROM parcelas
            " . $where . "
            ORDER BY data_vencimento ASC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return [
            'parcelas' => $stmt->fetchAll(),
            'total' => $totalRegistros,
            'pagina' => $pagina,
            'total_paginas' => ceil($totalRegistros / $itensPorPagina)
        ];
    }

    public function atualizarPai(int $instituicaoId, int $lancamentoId, array $dados) {
        $sql = "UPDATE lancamentos SET descricao = :desc, categoria_id = :cat, conta_fixa = :fixa WHERE id = :id AND instituicao_id = :inst";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'desc' => $dados['descricao'],
            'cat' => $dados['categoria_id'],
            'fixa' => isset($dados['conta_fixa']) && $dados['conta_fixa'] ? 1 : 0,
            'id' => $lancamentoId,
            'inst' => $instituicaoId
        ]);
    }

    // Excluir toda a cadeia (cascade deleta parcelas)
    public function excluir(int $instituicaoId, int $lancamentoId) {
        $sql = "DELETE FROM lancamentos WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $lancamentoId, 'instituicao_id' => $instituicaoId]);
    }
}
