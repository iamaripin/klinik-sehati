<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
 
            $table->string('doctor_code')->unique();
            $table->string('doctor_nik')->unique()->nullable();
            $table->string('doctor_tittle')->nullable();     // dr., drg., Sp.A, Sp.PD, dll
            $table->string('doctor_name');
            $table->string('doctor_suffix')->nullable();     // suffix nama belakang
            $table->string('doctor_prefix')->nullable();     // dr, drg, dsb
            $table->enum('doctor_sex', ['M', 'F'])->nullable();
            $table->date('doctor_dob')->nullable();
            $table->string('doctor_phone', 20)->nullable();
            $table->text('doctor_address')->nullable();
            $table->string('medical_code')->nullable();      // kode poli/spesialis
 
            $table->string('doctor_email')->nullable();
            $table->string('doctor_photo')->nullable();      // foto/URL
            $table->boolean('is_active')->default(true);     // status dokter
            $table->string('specialist')->nullable();        // Spesialisasi text
            $table->string('sip_number')->nullable();        // Surat Izin Praktek
            $table->date('sip_expiry')->nullable();          // Masa berlaku SIP
 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
