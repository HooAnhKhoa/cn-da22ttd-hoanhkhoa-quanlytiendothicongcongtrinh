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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->string('site_code')->unique()->nullable();
            $table->string('site_name');
            $table->text('description')->nullable();
            
            // Ngân sách của site (tổng từ các tasks)
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0)->comment('Tổng đã thanh toán');
            $table->decimal('remaining_budget', 15, 2)->virtualAs('total_budget - paid_amount')->comment('Số tiền còn lại (tự động tính)');
            
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('progress_percent', 5, 2)->default(0);
            
            // Trạng thái site
            $table->enum('status', ['planned', 'pending_contract', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('planned');
            
            // Trạng thái thanh toán
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'fully_paid', 'overdue'])->default('unpaid');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('site_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};