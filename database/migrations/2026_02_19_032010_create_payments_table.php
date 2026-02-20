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

            $table->string('payment_no')->unique();

            $table->string('mr_code');
            $table->string('visit_no');
            $table->foreignId('bill_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('amount_paid', 15, 2);

            $table->enum('payment_method', [
                'cash',
                'transfer',
                'qris',
                'debit',
                'credit',
                'insurance'
            ]);

            $table->string('reference_number')->nullable();
            // no transfer / no approval

            $table->dateTime('payment_date');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
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
