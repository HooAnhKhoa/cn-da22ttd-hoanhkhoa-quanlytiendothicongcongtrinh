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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name')->unique();
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('contractor_id')->constrained('users');
            $table->foreignId('engineer_id')->constrained('users');
            $table->string('location');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('total_budget', 15, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('planned');
            $table->timestamps();

// - id
// - project_code
// - name
// - owner_id (FK → users where user_type='owner')
// - contractor_id (FK → contractors)
// - engineer_id (FK → employees)  ← kỹ sư chính
// - location
// - start_date
// - end_date
// - total_budget
// - description
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
        Schema::dropIfExists('projects');
    }
};
