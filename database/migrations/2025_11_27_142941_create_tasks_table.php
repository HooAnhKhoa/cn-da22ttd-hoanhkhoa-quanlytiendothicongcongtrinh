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
            $table->foreignId('assigned_engineer_id')->nullable()->constrained('users');
            
            $table->string('task_code')->unique()->nullable();
            $table->string('task_name');
            $table->text('description')->nullable();
            
            $table->decimal('task_budget', 15, 2)->default(0);
            
            $table->date('start_date');
            $table->date('end_date')->nullable();
            // Bỏ planned_duration, actual_duration (tính toán khi query)
            
            $table->unsignedTinyInteger('progress_percent')->default(0);
            
            $table->enum('status', ['planned', 'in_progress', 'pending_review', 'rejected', 'completed', 'on_hold', 'cancelled'])->default('planned');
            
            // Review
            $table->text('owner_review')->nullable();
            $table->tinyInteger('owner_rating')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_reviews');
    }
};