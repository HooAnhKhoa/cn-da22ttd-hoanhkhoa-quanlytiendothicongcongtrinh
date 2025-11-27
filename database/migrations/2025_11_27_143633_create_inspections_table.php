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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks');
            $table->foreignId('engineer_id')->constrained('users');
            $table->enum('result', ['pass', 'fail', 'rework']);
            $table->text('notes')->nullable();
            $table->date('date');
            $table->json('attached_files')->nullable();
//             inspections
// - id
// - task_id
// - engineer_id (FK → employees)
// - result
// - notes
// - date
// - attached_files (JSON)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
