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
        Schema::create('diagnosa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admission_id')
                ->constrained('admissions')
                ->cascadeOnDelete();
            $table->string('mr_code');
            $table->string('visit_no');
            // SOAP - Minimal
            $table->text('keluhan_utama')->nullable();
            $table->text('anamnesa_dokter')->nullable();
            $table->text('pemeriksaan_fisik')->nullable(); // Tambahan O
            $table->text('assessment')->nullable();        // Tambahan A ringkas

            // Diagnosa
            $table->string('diagnosa_icd', 20)->nullable();

            // Tindakan
            $table->string('tindakan_icd', 20)->nullable();

            // Plan
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->date('kontrol_kembali')->nullable(); // Tambahan penting

            // Audit (lebih proper)
            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('recorded_at')->useCurrent();

            $table->boolean('is_final')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosa');
    }
};
