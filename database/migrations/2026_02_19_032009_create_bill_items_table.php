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
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bill_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('item_type');
            // contoh: 'medicine', 'service', 'lab', 'admin'

            $table->unsignedBigInteger('reference_id')->nullable();
            // bisa refer ke patient_order_detail, tindakan, dll

            $table->string('description');

            $table->integer('qty')->default(1);
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
