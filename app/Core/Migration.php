<?php

namespace App\Core;

/**
 * Migration Manager
 * 数据库迁移管理器
 */
class Migration {
    /**
     * Database instance
     * @var DB
     */
    protected $db;

    /**
     * Migrations path
     * @var string
     */
    protected $migrationsPath;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = db();
        $this->migrationsPath = base_path('database/migrations');

        // Ensure migrations table exists
        $this->createMigrationsTable();
    }

    /**
     * Create migrations tracking table
     * 创建迁移跟踪表
     */
    protected function createMigrationsTable() {
        $this->db->execute("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Run pending migrations
     * 运行待执行的迁移
     *
     * @return array Results
     */
    public function run() {
        $migrations = $this->getPendingMigrations();
        $results = [];

        if (empty($migrations)) {
            return ['message' => 'No pending migrations'];
        }

        $batch = $this->getNextBatchNumber();

        foreach ($migrations as $migration) {
            try {
                $this->executeMigration($migration);

                // Record migration
                $this->db->table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch,
                ]);

                $results[] = "✓ Migrated: {$migration}";
            } catch (\Exception $e) {
                $results[] = "✗ Failed: {$migration} - " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Rollback last batch of migrations
     * 回滚最后一批迁移
     *
     * @return array Results
     */
    public function rollback() {
        $batch = $this->getLastBatchNumber();

        if ($batch === 0) {
            return ['message' => 'Nothing to rollback'];
        }

        $migrations = $this->db->table('migrations')
            ->where('batch', $batch)
            ->orderBy('id', 'DESC')
            ->get();

        $results = [];

        foreach ($migrations as $migration) {
            try {
                $this->executeMigrationDown($migration['migration']);

                // Remove from migrations table
                $this->db->table('migrations')
                    ->where('migration', $migration['migration'])
                    ->delete();

                $results[] = "✓ Rolled back: {$migration['migration']}";
            } catch (\Exception $e) {
                $results[] = "✗ Failed: {$migration['migration']} - " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Reset all migrations
     * 重置所有迁移
     *
     * @return array Results
     */
    public function reset() {
        $results = [];

        while ($this->getLastBatchNumber() > 0) {
            $rollbackResults = $this->rollback();
            $results = array_merge($results, $rollbackResults);
        }

        return $results;
    }

    /**
     * Get all migrations
     * 获取所有迁移文件
     *
     * @return array
     */
    public function getAllMigrations() {
        $files = glob($this->migrationsPath . '/*.php');
        $migrations = [];

        foreach ($files as $file) {
            $migrations[] = basename($file, '.php');
        }

        sort($migrations);
        return $migrations;
    }

    /**
     * Get pending migrations
     * 获取待执行的迁移
     *
     * @return array
     */
    public function getPendingMigrations() {
        $all = $this->getAllMigrations();
        $executed = $this->db->table('migrations')
            ->select(['migration'])
            ->get();

        $executedNames = array_column($executed, 'migration');

        return array_diff($all, $executedNames);
    }

    /**
     * Get migration status
     * 获取迁移状态
     *
     * @return array
     */
    public function getStatus() {
        $all = $this->getAllMigrations();
        $executed = $this->db->table('migrations')
            ->select(['migration', 'batch'])
            ->get();

        $executedMap = [];
        foreach ($executed as $item) {
            $executedMap[$item['migration']] = $item['batch'];
        }

        $status = [];
        foreach ($all as $migration) {
            $status[] = [
                'migration' => $migration,
                'status' => isset($executedMap[$migration]) ? 'Migrated' : 'Pending',
                'batch' => $executedMap[$migration] ?? null,
            ];
        }

        return $status;
    }

    /**
     * Execute a migration (up)
     * 执行迁移（向上）
     *
     * @param string $migration
     * @return void
     */
    protected function executeMigration($migration) {
        require_once $this->migrationsPath . '/' . $migration . '.php';

        // Call up() function
        if (function_exists('up')) {
            up($this->db);
        }
    }

    /**
     * Execute migration down (rollback)
     * 执行迁移回滚（向下）
     *
     * @param string $migration
     * @return void
     */
    protected function executeMigrationDown($migration) {
        require_once $this->migrationsPath . '/' . $migration . '.php';

        // Call down() function
        if (function_exists('down')) {
            down($this->db);
        }
    }

    /**
     * Get next batch number
     * 获取下一个批次号
     *
     * @return int
     */
    protected function getNextBatchNumber() {
        $lastBatch = $this->db->query(
            "SELECT MAX(batch) as max_batch FROM migrations"
        );

        return ($lastBatch[0]['max_batch'] ?? 0) + 1;
    }

    /**
     * Get last batch number
     * 获取最后批次号
     *
     * @return int
     */
    protected function getLastBatchNumber() {
        $lastBatch = $this->db->query(
            "SELECT MAX(batch) as max_batch FROM migrations"
        );

        return $lastBatch[0]['max_batch'] ?? 0;
    }
}
