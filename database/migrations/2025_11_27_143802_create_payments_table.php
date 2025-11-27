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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts');
            $table->decimal('amount', 15, 2);
            $table->date('pay_date');
            $table->enum('method', ['bank_transfer', 'credit_card', 'cash', 'check']);
            $table->text('note')->nullable();

//             payments
// - id
// - contract_id
// - amount
// - pay_date
// - method
// - note

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
