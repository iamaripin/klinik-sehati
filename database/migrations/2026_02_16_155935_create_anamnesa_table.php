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
        Schema::create('anamnesa', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relasi (sesuaikan nama/tipe PK di tabel lain)
            $table->string('mr_code')->index();
            $table->string('visit_no')->nullable()->index(); // kunjungan / admission jika ada
            $table->string('recorded_by')->nullable()->index(); // user/staff yang merekam
 
            // Vital sign utama
            $table->decimal('temperature', 5, 2)->nullable()->comment('derajat Celsius, ex: 36.50');

            // Tekanan darah
            $table->unsignedSmallInteger('bp_systolic')->nullable();
            $table->unsignedSmallInteger('bp_diastolic')->nullable();
 
            // Anthropometri
            $table->decimal('weight_kg', 6, 2)->nullable()->comment('berat dalam kg');
            $table->decimal('height_cm', 6, 2)->nullable()->comment('tinggi dalam cm');
            $table->decimal('bmi', 5, 2)->nullable();

            // Keterangan tambahan / source / device info / flags
            $table->string('anamnesa')->nullable()->comment('Catatan tambahan dari pemeriksa');

            // Soft delete & timestamps
            $table->softDeletes();
            $table->timestamps();

            // Foreign keys (opsional: aktifkan jika tabel ada dan ingin integritas)
            $table->foreign('mr_code')->references('mr_code')->on('patients')->onDelete('cascade');
            $table->foreign('visit_no')->references('visit_no')->on('admissions')->onDelete('set null');
            $table->foreign('recorded_by')->references('username')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anamnesa');
    }
};
