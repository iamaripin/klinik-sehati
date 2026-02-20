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
        Schema::create('patient_orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_no')->unique();

            $table->string('mr_code');   // kode rekam medis pasien
            $table->string('visit_no');  // nomor kunjungan

            $table->dateTime('order_date');

            $table->decimal('total_amount', 15, 2)->default(0);

            $table->enum('status', ['draft', 'completed', 'cancelled'])
                ->default('draft');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['mr_code', 'visit_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_orders');
    }
};
