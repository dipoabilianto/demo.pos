<?php

namespace Tests\Support;

use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\DB;

trait FixSQLiteTransactionLeak
{
    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->migrateDatabases();

            $this->app[\Illuminate\Contracts\Console\Kernel::class]->setArtisan(null);

            $this->updateLocalCacheOfInMemoryDatabases();

            RefreshDatabaseState::$migrated = true;
        }

        $this->cleanupOrphanedTransaction();

        $this->beginDatabaseTransaction();
    }

    private function cleanupOrphanedTransaction(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        try {
            $pdo = DB::connection()->getPdo();
            if ($pdo) {
                $pdo->exec('ROLLBACK');
            }
        } catch (\Exception) {
        }
    }
}
