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
        Schema::create('task_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks');
            $table->foreignId('reviewer_id')->constrained('users')->comment('Người đánh giá (owner)');
            
            // Đánh giá
            $table->tinyInteger('rating')->nullable()->comment('Điểm từ 1-5');
            $table->text('comments')->nullable();
            $table->text('improvement_suggestions')->nullable()->comment('Gợi ý cải thiện');
            
            // Kết quả đánh giá
            $table->enum('result', ['approved', 'rejected', 'needs_revision'])->default('needs_revision');
            
            // Thông tin revision
            $table->boolean('requires_rework')->default(false);
            $table->text('rework_instructions')->nullable();
            $table->date('rework_deadline')->nullable();
            
            // Thông tin phê duyệt
            $table->boolean('is_final')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            // File đính kèm (hình ảnh, tài liệu đánh giá)
            $table->json('review_files')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['task_id', 'result']);
            $table->index(['reviewer_id', 'result']);
            $table->index('requires_rework');
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