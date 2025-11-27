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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites');
            $table->foreignId('parent_id')->nullable()->constrained('tasks');
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('planned_duration')->nullable();
            $table->integer('actual_duration')->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->enum('status', ['planned', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('planned');
            $table->timestamps();

// - id
// - site_id (FK → sites)
// - parent_id (FK → tasks.id)
// - task_code
// - name
// - description
// - assigned_engineer_id (FK → employees)
// - start_date
// - end_date
// - planned_duration
// - actual_duration
// - progress_percent
// - status
// - created_at
// - updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
