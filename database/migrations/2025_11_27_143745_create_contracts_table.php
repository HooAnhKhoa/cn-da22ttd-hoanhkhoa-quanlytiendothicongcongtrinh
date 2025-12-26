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
            
            $table->decimal('contract_value', 15, 2);
            $table->decimal('advance_payment', 15, 2)->default(0);
            $table->date('signed_date');
            $table->date('due_date');
            
            // Trạng thái hợp đồng
            $table->enum('status', ['draft', 'pending_signature', 'active', 'completed', 'terminated', 'on_hold', 'expired'])->default('draft');
            
            // Thông tin hợp đồng
            $table->string('contract_number')->unique()->nullable();
            $table->string('contract_name')->nullable();
            $table->text('description')->nullable();
            
            // File
            $table->string('contract_file_path')->nullable();
            $table->string('contract_file_name')->nullable();
            $table->json('additional_files')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
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