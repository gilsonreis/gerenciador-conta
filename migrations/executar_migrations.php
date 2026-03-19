<?php
/**
 * migrations/executar_migrations.php
 * 
 * Executor de migrations com controle de execução via tabela `migrations`.
 * Cada arquivo é executado apenas UMA VEZ — migrações já registradas são ignoradas.
 * 
 * USO:
 *   php migrations/executar_migrations.php             (executa migrações pendentes)
 *   php migrations/executar_migrations.php --dry-run  (apenas lista, não executa)
 *   php migrations/executar_migrations.php --status   (lista o status de cada migration)
 */

$raiz = __DIR__ . '/../';
require_once $raiz . 'config/Database.php';

$args   = $argv ?? [];
$dryRun = in_array('--dry-run', $args);
$status = in_array('--status', $args);
$thisFile = basename(__FILE__);

$db = Database::getConnection();

// --- Criar tabela de controle se não existir ---
$db->exec("
    CREATE TABLE IF NOT EXISTS `migrations` (
        `id`          INT(11)      NOT NULL AUTO_INCREMENT,
        `arquivo`     VARCHAR(255) NOT NULL,
        `executada_em` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `arquivo` (`arquivo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// --- Carregar migrações já executadas ---
$executadas = $db->query("SELECT arquivo FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
$executadas = array_flip($executadas); // para lookup O(1)

// --- Listar arquivos de migration ---
$arquivos = glob(__DIR__ . '/*.php');
$migrations = [];
foreach ($arquivos as $path) {
    if (basename($path) === $thisFile) continue;
    $migrations[] = $path;
}
sort($migrations); // ordem pelo nome de arquivo (timestamp no prefixo)

if (empty($migrations)) {
    echo "[INFO] Nenhuma migration encontrada.\n";
    exit;
}

echo "==============================\n";
echo " Executor de Migrations\n";
echo " Modo: " . ($dryRun ? "DRY RUN" : ($status ? "STATUS" : "EXECUÇÃO")) . "\n";
echo " Total: " . count($migrations) . " arquivo(s)\n";
echo "==============================\n\n";

// --- Modo --status: listar estado de cada arquivo ---
if ($status) {
    foreach ($migrations as $path) {
        $nome = basename($path);
        $estado = isset($executadas[$nome]) ? "✓ Executada" : "○ Pendente";
        echo "  [{$estado}] {$nome}\n";
    }
    exit;
}

$pendentes = 0;
$ignoradas = 0;

foreach ($migrations as $path) {
    $nome = basename($path);

    if (isset($executadas[$nome])) {
        echo "  [SKIP] {$nome}\n";
        $ignoradas++;
        continue;
    }

    echo "▶ {$nome}\n";
    $pendentes++;

    if (!$dryRun) {
        try {
            include $path;

            // Registrar execução bem-sucedida
            $stmt = $db->prepare("INSERT IGNORE INTO migrations (arquivo) VALUES (:arquivo)");
            $stmt->execute(['arquivo' => $nome]);

            echo "  ✓ Executada com sucesso.\n\n";
        } catch (Throwable $e) {
            echo "  ✗ ERRO: " . $e->getMessage() . "\n\n";
            echo "[ABORTADO] Corrija o erro antes de continuar.\n";
            exit(1);
        }
    } else {
        echo "  (dry-run, não executado)\n\n";
    }
}

echo "==============================\n";
if ($dryRun) {
    echo " Simulação: {$pendentes} pendente(s), {$ignoradas} já executada(s).\n";
} else {
    echo " Concluído: {$pendentes} executada(s), {$ignoradas} ignorada(s).\n";
}
echo "==============================\n";
