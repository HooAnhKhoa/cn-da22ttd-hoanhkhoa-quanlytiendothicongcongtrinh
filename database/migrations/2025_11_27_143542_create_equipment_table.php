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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_name');
            $table->string('type');
            $table->string('serial')->unique();
            $table->enum('status', ['available', 'in_use', 'under_maintenance'])->default('available');
            $table->string('location'); 
//             equipment
// - id
// - name
// - type
// - serial
// - status
// - location

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
