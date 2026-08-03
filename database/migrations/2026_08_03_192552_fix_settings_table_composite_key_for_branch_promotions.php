<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `key` alone is the primary key today, so a branch-specific row
     * (e.g. 'promotions' for branch 6) can never coexist with the
     * global row (branch_id null) of the same key. Switch to a
     * surrogate id and a (key, branch_id) unique index so per-branch
     * overrides are possible.
     */
    public function up(): void
    {
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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['key', 'branch_id']);
            $table->dropColumn('id');
            $table->primary('key');
        });
    }
};
