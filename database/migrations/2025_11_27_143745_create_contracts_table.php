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
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('contractor_id')->constrained('users');
            $table->decimal('contract_value', 15, 2);
            $table->date('signed_date');
            $table->date('due_date');
            $table->enum('status', ['active', 'completed', 'terminated'])->default('active');

// contracts
// - id
// - project_id
// - contractor_id (FK)
// - contract_value
// - signed_date
// - due_date
// - status

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
