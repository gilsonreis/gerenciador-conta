<?php
require_once __DIR__ . '/../../config/Database.php';

class SnapshotRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Salva (ou atualiza) o snapshot de abertura de uma conta para um mês.
     * Idempotente: pode ser chamado múltiplas vezes sem duplicar.
     */
    public function salvarSnapshot(int $contaId, float $valorAbertura, string $mesReferencia): bool {
        $sql = "
            INSERT INTO snapshots_saldos (conta_id, valor_abertura, mes_referencia)
            VALUES (:conta_id, :valor_abertura, :mes_referencia)
            ON DUPLICATE KEY UPDATE valor_abertura = VALUES(valor_abertura)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'conta_id'       => $contaId,
            'valor_abertura' => $valorAbertura,
            'mes_referencia' => $mesReferencia,
        ]);
    }

    /**
     * Retorna a soma de todos os saldos de abertura do mês para a instituição.
     * Se não houver snapshot, retorna null (para ativar o fallback no dashboard).
     */
    public function somaAberturaMes(int $instituicaoId, string $mesAno): ?float {
        // mes_referencia é sempre o dia 01 do mês
        $mesReferencia = $mesAno . '-01';

        $sql = "
            SELECT SUM(ss.valor_abertura) as total_abertura
            FROM snapshots_saldos ss
            JOIN contas c ON ss.conta_id = c.id
            WHERE (:instituicao_id = 0 OR c.instituicao_id = :instituicao_id)
            AND ss.mes_referencia = :mes_referencia
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'instituicao_id' => $instituicaoId,
            'mes_referencia' => $mesReferencia,
        ]);
        $row = $stmt->fetch();

        if ($row === false || $row['total_abertura'] === null) {
            return null; // Nenhum snapshot encontrado para este mês
        }

        return (float)$row['total_abertura'];
    }

    /**
     * Retorna todos os snapshots de um mês para uma instituição (debug/relatórios).
     */
    public function listarPorMes(int $instituicaoId, string $mesAno): array {
        $mesReferencia = $mesAno . '-01';
        $sql = "
            SELECT ss.id, c.nome as conta_nome, ss.valor_abertura, ss.mes_referencia, ss.criado_em
            FROM snapshots_saldos ss
            JOIN contas c ON ss.conta_id = c.id
            WHERE (:instituicao_id = 0 OR c.instituicao_id = :instituicao_id)
            AND ss.mes_referencia = :mes_referencia
            ORDER BY c.nome
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'instituicao_id' => $instituicaoId,
            'mes_referencia' => $mesReferencia,
        ]);
        return $stmt->fetchAll();
    }
}
