<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('mr_code')->unique();               // Nomor rekam medis
            $table->string('patient_nik')->nullable();         // NIK
            $table->string('patient_name');
            $table->enum('patient_sex', ['male', 'female']);
            $table->date('patient_dob')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_email')->nullable();
            $table->text('patient_address')->nullable();

            $table->string('patient_religion')->nullable();    // agama
            $table->string('patient_job')->nullable();         // pekerjaan
            $table->string('patient_status')->nullable();      // menikah/belum
            $table->string('patient_blood')->nullable();       // gol. darah

            $table->string('patient_emergency_contact')->nullable();
            $table->string('patient_alergy')->nullable();      // alergi
            $table->string('patient_special')->nullable();     // kondisi khusus (hamil, disabilitas, dsb)
            $table->text('patient_notes')->nullable();         // catatan tambahan
 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
