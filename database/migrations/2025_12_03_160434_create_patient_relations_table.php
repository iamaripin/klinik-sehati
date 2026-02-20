<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patient_relations', function (Blueprint $table) {
            $table->id();

            $table->string('relation_code');                   // mr_code pasien induk
            $table->string('relation_nik')->nullable();
            $table->string('relation_name');
            $table->enum('relation_sex', ['male', 'female'])->nullable();
            $table->date('relation_dob')->nullable();
            $table->string('relation_phone')->nullable();
            $table->text('relation_address')->nullable();
            $table->string('relation_blood')->nullable();

            $table->timestamps();

            // foreign key tanpa cascade (opsional)
            $table->foreign('relation_code')
                ->references('mr_code')
                ->on('patients')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_relations');
    }
};
