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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('contractor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('contract_value', 15, 2);
            $table->decimal('advance_payment', 15, 2)->default(0)->comment('Tạm ứng ban đầu');
            $table->date('signed_date');
            $table->date('due_date');
            
            // Trạng thái hợp đồng
            $table->enum('status', ['draft', 'pending_signature', 'active', 'completed', 'terminated', 'on_hold', 'expired'])->default('draft');
            
            // Trạng thái thanh toán
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'fully_paid', 'overdue', 'refunded'])->default('unpaid');
            
            // Thông tin thanh toán
            $table->decimal('total_paid', 15, 2)->default(0)->comment('Tổng đã thanh toán');
            $table->decimal('remaining_amount', 15, 2)->storedAs('contract_value - total_paid')->comment('Số tiền còn lại (tự động tính)');
            
            // Thông tin hợp đồng
            $table->string('contract_number')->unique()->nullable()->comment('Số hợp đồng');
            $table->string('contract_name')->nullable()->comment('Tên hợp đồng');
            $table->text('description')->nullable();
            
            // Lưu trữ file hợp đồng
            $table->string('contract_file_path')->nullable()->comment('Đường dẫn file hợp đồng chính');
            $table->string('contract_file_name')->nullable()->comment('Tên file hợp đồng');
            $table->string('contract_file_size')->nullable()->comment('Kích thước file (bytes)');
            $table->string('contract_file_mime')->nullable()->comment('Loại file (pdf, docx, v.v.)');
            
            // File hợp đồng bổ sung (có thể có nhiều file)
            $table->json('additional_files')->nullable()->comment('Danh sách file bổ sung');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index để tối ưu truy vấn
            $table->index(['owner_id', 'status']);
            $table->index(['contractor_id', 'status']);
            $table->index(['payment_status', 'due_date']);
            $table->index('contract_number');
            $table->index(['project_id', 'status']);
            $table->index('contract_name');
            $table->index('signed_date');
            $table->index(['status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};