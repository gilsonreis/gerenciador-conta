<?php
require_once __DIR__ . '/../../config/Database.php';

class ParcelaRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function alternarStatus(int $instituicaoId, int $parcelaId) {
        // Double check instituicao rules via inner join
        $sql = "
            UPDATE parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            SET p.status = IF(p.status = 'pendente', 'pago', 'pendente')
            WHERE p.id = :id AND l.instituicao_id = :instituicao_id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $parcelaId, 'instituicao_id' => $instituicaoId]);
    }
    
    public function atualizar(int $instituicaoId, int $parcelaId, array $dados) {
        // Update just the parcela specifics e.g., value, date or status if necessary.
        // Wait, does the form edit the parent or just the parcela?
        // Let's assume the user edits just this single installment.
        $valor = (float)str_replace(',', '.', str_replace('.', '', $dados['valor']));
        $sql = "
            UPDATE parcelas p
            JOIN lancamentos l ON p.lancamento_id = l.id
            SET p.valor = :val, p.data_vencimento = :venc, p.status = :status,
                l.descricao = :desc, l.categoria_id = :cat, l.conta_fixa = :fixa
            WHERE p.id = :id AND l.instituicao_id = :instituicao_id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'val' => $valor,
            'venc' => $dados['data_vencimento'],
            'status' => $dados['status'] ?? 'pendente',
            'desc' => $dados['descricao'],
            'cat' => $dados['categoria_id'],
            'fixa' => isset($dados['conta_fixa']) && $dados['conta_fixa'] ? 1 : 0,
            'id' => $parcelaId,
            'instituicao_id' => $instituicaoId
        ]);
    }
}
