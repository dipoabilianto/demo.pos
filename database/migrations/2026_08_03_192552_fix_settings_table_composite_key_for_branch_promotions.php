<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `key` alone is the primary key today, so a branch-specific row
     * (e.g. 'promotions' for branch 6) can never coexist with the
     * global row (branch_id null) of the same key. Switch to a
     * surrogate id and a (key, branch_id) unique index so per-branch
     * overrides are possible.
     *
     * SQLite refuses to ALTER TABLE ... ADD an autoincrement primary key
     * column onto an existing table, so it needs a full table rebuild
     * instead of the in-place ALTER that works on MySQL/MariaDB.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::create('settings_rebuild', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->string('key');
                $table->text('value')->nullable();
                $table->timestamps();
                $table->unique(['key', 'branch_id']);
            });

            DB::statement('INSERT INTO settings_rebuild (branch_id, "key", value, created_at, updated_at) SELECT branch_id, "key", value, created_at, updated_at FROM settings');

            Schema::drop('settings');
            Schema::rename('settings_rebuild', 'settings');

            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->id()->first();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->unique(['key', 'branch_id']);
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::create('settings_rebuild', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            DB::statement('INSERT INTO settings_rebuild ("key", branch_id, value, created_at, updated_at) SELECT "key", branch_id, value, created_at, updated_at FROM settings GROUP BY "key"');

            Schema::drop('settings');
            Schema::rename('settings_rebuild', 'settings');

            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['key', 'branch_id']);
            $table->dropColumn('id');
            $table->primary('key');
        });
    }
};
