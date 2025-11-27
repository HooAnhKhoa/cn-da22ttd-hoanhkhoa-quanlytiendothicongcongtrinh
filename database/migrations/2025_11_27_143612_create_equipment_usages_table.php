<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks');
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->foreignId('engineer_id')->constrained('users');
//             equipment_usage
// - id
// - task_id
// - equipment_id
// - start_time
// - end_time
// - engineer_id (FK → employees)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_usages');
    }
};
