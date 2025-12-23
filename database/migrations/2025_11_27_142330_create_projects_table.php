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
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'pending_contract', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('draft');
            $table->timestamps();

            // Indexes
            $table->index(['owner_id', 'status']);
            $table->index(['contractor_id', 'status']);
            $table->index(['status', 'start_date']);
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