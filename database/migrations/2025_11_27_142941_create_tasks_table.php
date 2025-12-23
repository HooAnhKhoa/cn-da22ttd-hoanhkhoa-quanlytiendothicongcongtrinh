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
            $table->decimal('task_budget', 15, 2)->default(0)->comment('Ngân sách cho task này');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('planned_duration')->nullable();
            $table->integer('actual_duration')->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            
            // Trạng thái thực hiện
            $table->enum('status', ['planned', 'in_progress', 'pending_review', 'rejected', 'completed', 'on_hold', 'cancelled'])->default('planned');
            
            // Trạng thái thanh toán cho task
            $table->enum('payment_status', ['unpaid', 'pending_payment', 'paid', 'overdue'])->default('unpaid');
            
            // Đánh giá từ owner
            $table->text('owner_review')->nullable()->comment('Đánh giá từ chủ đầu tư');
            $table->tinyInteger('owner_rating')->nullable()->comment('Điểm đánh giá từ 1-5');
            $table->boolean('is_approved')->default(false)->comment('Đã được owner chấp nhận');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['site_id', 'status']);
            $table->index(['assigned_engineer_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('task_code');
            $table->index('is_approved');
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