<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('icd10', function (Blueprint $table) {
            $table->id();

            // Kode ICD-10
            $table->string('code', 10)->unique(); // contoh: A00, E11.9

            // Deskripsi
            $table->string('description', 255);
            // Chapter (opsional tapi sangat berguna)
            $table->string('chapter', 100)->nullable();

            // Blok kategori (misal A00-A09)
            $table->string('block', 20)->nullable();

            // Flag
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index untuk pencarian cepat
            $table->index('code');
            $table->index('description');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('icd10');
    }
};
