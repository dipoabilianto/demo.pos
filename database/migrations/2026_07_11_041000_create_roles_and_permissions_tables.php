<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->json('permissions');
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
