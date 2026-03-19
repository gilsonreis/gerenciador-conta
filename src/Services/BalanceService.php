<?php
/**
 * BalanceService — Serviço Central de Cálculo de Saldo
 *
 * REGRA DE OURO:
 *   Saldo Atual = Snapshot(Abertura do Mês) + Entradas(Mês) - Saídas Pagas(Mês)
 *
 * Se não houver snapshot para o mês, usa fallback:
 *   Saldo Histórico Acumulado - Entradas do Mês
 *
 * USE ESTE SERVIÇO em todo lugar que precisar do saldo de uma conta.
 * NÃO use ContaRepository::saldos() para novos consumidores — ele está marcado
 * como @deprecated e existe apenas para o cron de snapshots.
 */

require_once __DIR__ . '/../Repositories/ContaRepository.php';
require_once __DIR__ . '/../Repositories/SnapshotRepository.php';
require_once __DIR__ . '/../../config/Database.php';

class BalanceService {
    private PDO $db;
    private SnapshotRepository $snapshotRepo;
    private ContaRepository $contaRepo;

    public function __construct() {
        $this->db           = Database::getConnection();
        $this->snapshotRepo = new SnapshotRepository();
        $this->contaRepo    = new ContaRepository();
    }

    /**
     * Retorna o saldo atual de uma conta para o mês especificado.
     *
     * @param int    $contaId  ID da conta
     * @param string $mesAno   Formato 'YYYY-MM' (default: mês atual)
     * @return float
     */
    public function getSaldoAtual(int $contaId, string $mesAno = null): float {
        $mesAno = $mesAno ?? date('Y-m');

        // 1. Snapshot de abertura da conta no mês
        $abertura = $this->getSnapshotConta($contaId, $mesAno);

        // 2. Fallback: saldo histórico acumulado - entradas do mês
        if ($abertura === null) {
            $historico = $this->getSaldoHistoricoAcumulado($contaId);
            $entradasMes = $this->getEntradasContaMes($contaId, $mesAno);
            $abertura = $historico - $entradasMes;
        }

        // 3. Movimentação do mês
        $entradasMes  = $this->getEntradasContaMes($contaId, $mesAno);
        $saidasPagas  = $this->getSaidasPagasContaMes($contaId, $mesAno);

        return $abertura + $entradasMes - $saidasPagas;
    }

    /**
     * Retorna a lista de todas as contas de uma instituição com o saldo mensal calculado.
     * Substitui ContaRepository::saldos() nos endpoints de listagem.
     *
     * @param int    $instituicaoId
     * @param string $mesAno   Formato 'YYYY-MM' (default: mês atual)
     * @return array
     */
    public function getSaldosInstituicao(int $instituicaoId, string $mesAno = null): array {
        $mesAno = $mesAno ?? date('Y-m');

        $contas = $this->contaRepo->listar($instituicaoId);
        $resultado = [];

        foreach ($contas as $conta) {
            $saldoAtual = $this->getSaldoAtual((int)$conta['id'], $mesAno);
            $resultado[] = [
                'id'                   => $conta['id'],
                'nome'                 => $conta['nome'],
                'saldo_atual_real'     => $saldoAtual,
                'saldo_atual_formatado'=> 'R$ ' . number_format($saldoAtual, 2, ',', '.'),
            ];
        }

        return $resultado;
    }

    // ─── Helpers Privados ───────────────────────────────────────────────────

    /**
     * Busca o snapshot de abertura de UMA conta específica para o mês.
     * Retorna null se não houver snapshot.
     */
    private function getSnapshotConta(int $contaId, string $mesAno): ?float {
        $mesReferencia = $mesAno . '-01';
        $sql = "SELECT valor_abertura FROM snapshots_saldos WHERE conta_id = :conta_id AND mes_referencia = :mes";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conta_id' => $contaId, 'mes' => $mesReferencia]);
        $row = $stmt->fetch();
        return ($row !== false) ? (float)$row['valor_abertura'] : null;
    }

    /**
     * Saldo histórico acumulado de uma conta (desde o início dos tempos).
     * Usado apenas como base para o fallback. Não chame diretamente.
     */
    private function getSaldoHistoricoAcumulado(int $contaId): float {
        $sql = "
            SELECT
                c.saldo_inicial
                + COALESCE((SELECT SUM(valor) FROM caixa_entradas WHERE conta_id = c.id), 0)
                - COALESCE((SELECT SUM(valor - desconto) FROM parcelas WHERE conta_pagamento_id = c.id AND data_pagamento IS NOT NULL), 0)
                + COALESCE((SELECT SUM(valor) FROM transferencias WHERE conta_destino_id = c.id), 0)
                - COALESCE((SELECT SUM(valor) FROM transferencias WHERE conta_origem_id = c.id), 0)
            AS saldo_historico
            FROM contas c WHERE c.id = :conta_id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conta_id' => $contaId]);
        $row = $stmt->fetch();
        return $row ? (float)$row['saldo_historico'] : 0.0;
    }

    /** Soma total de entradas de uma conta num mês. */
    private function getEntradasContaMes(int $contaId, string $mesAno): float {
        $sql = "SELECT COALESCE(SUM(valor), 0) as total FROM caixa_entradas WHERE conta_id = :conta_id AND DATE_FORMAT(data_entrada, '%Y-%m') = :mes";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conta_id' => $contaId, 'mes' => $mesAno]);
        return (float)$stmt->fetch()['total'];
    }

    /** Soma de saídas pagas de uma conta num mês. */
    private function getSaidasPagasContaMes(int $contaId, string $mesAno): float {
        $sql = "
            SELECT COALESCE(SUM(p.valor - p.desconto), 0) as total
            FROM parcelas p
            WHERE p.conta_pagamento_id = :conta_id
            AND p.data_pagamento IS NOT NULL
            AND DATE_FORMAT(p.data_pagamento, '%Y-%m') = :mes
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conta_id' => $contaId, 'mes' => $mesAno]);
        return (float)$stmt->fetch()['total'];
    }
}
