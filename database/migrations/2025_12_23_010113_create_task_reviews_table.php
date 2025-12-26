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
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users'); // Người đánh giá
            
            // Thông tin đánh giá cơ bản
            $table->unsignedTinyInteger('rating')->comment('Điểm đánh giá 1-5');
            $table->text('comments')->nullable(); // Lưu ý: 'comments' số nhiều để khớp với Seeder
            $table->text('improvement_suggestions')->nullable();
            
            // Kết quả đánh giá
            $table->string('result')->nullable(); // Vd: passed, needs_revision, failed
            
            // Xử lý làm lại (Rework)
            $table->boolean('requires_rework')->default(false);
            $table->text('rework_instructions')->nullable();
            $table->dateTime('rework_deadline')->nullable();
            
            // Trạng thái
            $table->boolean('is_final')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            
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