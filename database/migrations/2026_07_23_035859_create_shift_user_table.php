<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 1=Senin .. 7=Minggu
            $table->timestamps();

            $table->unique(['shift_id', 'user_id', 'day_of_week']);
        });

        Schema::dropIfExists('shift_assignments');
    }

    public function down(): void
    {
        Schema::create('shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->timestamps();
        });

        Schema::dropIfExists('shift_user');
    }
};
