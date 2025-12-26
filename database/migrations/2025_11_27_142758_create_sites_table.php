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
            
            // Ngân sách (User tự nhập hoặc sum từ task, nhưng nên giữ 1 cột này để làm mốc so sánh)
            $table->decimal('total_budget', 15, 2)->default(0);
            
            $table->date('start_date');
            $table->date('end_date')->nullable();
            
            // Tiến độ (Có thể giữ để nhập tay hoặc tính toán)
            $table->decimal('progress_percent', 5, 2)->default(0);
            
            $table->enum('status', ['planned', 'pending_contract', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('planned');
            
            $table->timestamps();
            $table->softDeletes();
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