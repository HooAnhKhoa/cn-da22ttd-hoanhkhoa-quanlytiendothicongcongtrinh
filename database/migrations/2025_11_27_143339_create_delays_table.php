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
        Schema::create('delays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks');
            $table->text('reason');
            $table->integer('delay_days');
            $table->date('reported_date');
            $table->foreignId('responsible_engineer')->constrained('users');

// delays
// - id
// - task_id
// - reason
// - delay_days
// - reported_date
// - responsible_engineer (FK → employees)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delays');
    }
};
