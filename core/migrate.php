<?php
/**
 * TackPad migration runner.
 *
 * Applies every pending file in /migrations exactly once, in filename order,
 * tracking what has run in a `schema_migrations` table. Safe to run repeatedly
 * (already-applied migrations are skipped; each migration is also written to be
 * idempotent on its own). Designed for autonomous deployment over SSH:
 *
 *     php core/migrate.php            # apply all pending migrations
 *     php core/migrate.php --status   # list applied / pending without changing
 *
 * Migrations are PHP files returning ['name' => string, 'up' => callable(PDO)].
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$host = $_ENV['DB_SERVER'] ?? '127.0.0.1';
$name = $_ENV['DB_NAME'] ?? 'TackPad';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';

try {
    $db = new PDO("mysql:host={$host};dbname={$name}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "Cannot connect to database: {$e->getMessage()}\n");
    exit(1);
}

/** True when $table.$column exists in the current database. */
function columnExists(PDO $db, string $table, string $column): bool
{
    $stmt = $db->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c'
    );
    $stmt->execute([':t' => $table, ':c' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

// Ledger of applied migrations.
$db->exec(
    'CREATE TABLE IF NOT EXISTS `schema_migrations` (
        `name` VARCHAR(255) NOT NULL PRIMARY KEY,
        `applied_at` DATETIME NOT NULL
    )'
);

$applied = $db->query('SELECT name FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
$appliedSet = array_flip($applied);

$files = glob(__DIR__ . '/../migrations/*.php');
sort($files);

$statusOnly = in_array('--status', $argv, true);

echo "TackPad migrations ({$name})\n";
echo str_repeat('-', 40) . "\n";

$pending = 0;
foreach ($files as $file) {
    $migration = require $file;
    $mname = $migration['name'] ?? basename($file, '.php');

    if (isset($appliedSet[$mname])) {
        echo "  [applied] {$mname}\n";
        continue;
    }

    $pending++;
    if ($statusOnly) {
        echo "  [pending] {$mname}\n";
        continue;
    }

    try {
        $result = ($migration['up'])($db);
        $insert = $db->prepare('INSERT INTO schema_migrations (name, applied_at) VALUES (:n, NOW())');
        $insert->execute([':n' => $mname]);

        $detail = is_array($result) ? implode(', ', $result) : (string) $result;
        echo "  [run]     {$mname} — {$detail}\n";
    } catch (Throwable $e) {
        fwrite(STDERR, "  [FAIL]    {$mname} — {$e->getMessage()}\n");
        exit(1);
    }
}

echo str_repeat('-', 40) . "\n";
echo $statusOnly
    ? "{$pending} pending migration(s).\n"
    : "Done. " . ($pending === 0 ? "Nothing to apply." : "{$pending} migration(s) applied.") . "\n";
