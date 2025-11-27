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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->string('category');
            $table->string('document_name');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->dateTime('uploaded_at');
//             documents
// - id
// - project_id
// - category
// - name
// - file_path
// - uploaded_by (FK → users)
// - uploaded_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
