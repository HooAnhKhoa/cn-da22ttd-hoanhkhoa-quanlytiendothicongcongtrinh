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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->nullable()->constrained('contracts');
            $table->foreignId('task_id')->nullable()->constrained('tasks');
            $table->foreignId('site_id')->nullable()->constrained('sites');
            $table->foreignId('project_id')->nullable()->constrained('projects');
            
            // Thông tin thanh toán
            $table->decimal('amount', 15, 2);
            $table->date('pay_date');
            $table->enum('method', ['bank_transfer', 'credit_card', 'cash', 'check']);
            $table->string('transaction_code')->nullable()->unique()->comment('Mã giao dịch ngân hàng');
            
            // Loại thanh toán
            $table->enum('payment_type', ['advance', 'milestone', 'final', 'retention', 'other'])->default('milestone');
            
            // Trạng thái thanh toán
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            
            // Thông tin bổ sung
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            // File đính kèm (biên lai, chứng từ)
            $table->string('receipt_file_path')->nullable();
            $table->string('receipt_file_name')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['contract_id', 'status']);
            $table->index(['task_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['payment_type', 'pay_date']);
            $table->index('transaction_code');
            $table->index(['status', 'pay_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};