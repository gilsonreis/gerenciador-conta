<?php
require_once __DIR__ . '/../../config/Database.php';

class LancamentoRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarPorMes(int $instituicaoId, string $mesAno) {
        // $mesAno formato 'YYYY-MM'
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
                p.data_vencimento,
                p.status
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            JOIN categorias c ON l.categoria_id = c.id
            WHERE l.instituicao_id = :instituicao_id
            AND DATE_FORMAT(p.data_vencimento, '%Y-%m') = :mes_ano
            ORDER BY p.data_vencimento ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instituicao_id' => $instituicaoId, 'mes_ano' => $mesAno]);
        return $stmt->fetchAll();
    }

    public function resumoMes(int $instituicaoId, string $mesAno) {
        $sql = "
            SELECT 
                SUM(p.valor) as total_saidas,
                SUM(CASE WHEN l.conta_fixa = 1 THEN p.valor ELSE 0 END) as custo_vida
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            WHERE l.instituicao_id = :instituicao_id
            AND DATE_FORMAT(p.data_vencimento, '%Y-%m') = :mes_ano
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instituicao_id' => $instituicaoId, 'mes_ano' => $mesAno]);
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

            $sqlParcela = "INSERT INTO parcelas (lancamento_id, numero_parcela, total_parcelas, valor, data_vencimento, status) VALUES (:lanc, :num, :total, :val, :venc, :status)";
            $stmtParc = $this->db->prepare($sqlParcela);

            for ($i = $parcelaInicial; $i <= $totalParcelas; $i++) {
                $stmtParc->execute([
                    'lanc' => $lancamentoId,
                    'num' => $i,
                    'total' => $totalParcelas,
                    'val' => $valorParcela,
                    'venc' => $dataBase->format('Y-m-d'),
                    'status' => $dados['status'] ?? 'pendente'
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
                p.data_vencimento,
                p.status
            FROM parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            WHERE l.instituicao_id = :instituicao_id AND p.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['instituicao_id' => $instituicaoId, 'id' => $parcelaId]);
        return $stmt->fetch();
    }

    // Excluir toda a cadeia (cascade deleta parcelas)
    public function excluir(int $instituicaoId, int $lancamentoId) {
        $sql = "DELETE FROM lancamentos WHERE id = :id AND instituicao_id = :instituicao_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $lancamentoId, 'instituicao_id' => $instituicaoId]);
    }
}
