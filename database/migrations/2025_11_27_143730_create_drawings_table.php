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
        Schema::create('drawings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->string('code');
            $table->string('version');
            $table->string('file_path');
            $table->foreignId('approved_by')->constrained('users');
            $table->dateTime('approved_at')->nullable();
//             drawings
// - id
// - project_id
// - code
// - version
// - file_path
// - approved_by (FK → users)
// - approved_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drawings');
    }
};
