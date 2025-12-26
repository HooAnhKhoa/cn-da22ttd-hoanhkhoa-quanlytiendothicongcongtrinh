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
            // Một thanh toán thường gắn với hợp đồng hoặc một task cụ thể
            $table->foreignId('contract_id')->nullable()->constrained('contracts');
            $table->foreignId('task_id')->nullable()->constrained('tasks');
            
            // Thông tin thanh toán
            $table->decimal('amount', 15, 2);
            $table->date('pay_date');
            $table->enum('method', ['bank_transfer', 'credit_card', 'cash', 'check']);
            $table->string('transaction_code')->nullable()->unique();
            
            $table->enum('payment_type', ['advance', 'milestone', 'final', 'retention', 'other'])->default('milestone');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            
            $table->text('note')->nullable();
            
            // Người tạo/duyệt
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            // File
            $table->string('receipt_file_path')->nullable();
            $table->string('receipt_file_name')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
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