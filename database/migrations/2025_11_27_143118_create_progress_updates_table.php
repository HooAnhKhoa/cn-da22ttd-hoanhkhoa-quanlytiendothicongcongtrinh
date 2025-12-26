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
        Schema::create('progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks');
            $table->date('date');
            $table->unsignedTinyInteger('progress_percent');
            $table->text('description')->nullable();
            $table->json('attached_files')->nullable();
            $table->foreignId('created_by')->constrained('users');  
            $table->timestamps();
// - id
// - task_id (FK)
// - date
// - progress_percent
// - description
// - attached_files (JSON)
// - created_by (FK → users)
// - created_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_updates');
    }
};
