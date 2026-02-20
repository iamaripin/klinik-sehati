<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();

            $table->string('mr_code');
            $table->string('visit_no')->unique(); // JLN-mr_code-001
            $table->date('visit_date');
            $table->time('visit_time');

            $table->string('poli');
            $table->string('doctor_code');
            $table->enum('visit_type', ['IGD', 'POLI'])->default('POLI');
            $table->string('payment_type');
            $table->string('diagnosis')->nullable();
            
            $table->string('reservation_code')->nullable();
 
            $table->enum('status', ['REGISTERED', 'FINISHED', 'CANCELED'])->default('REGISTERED');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();

            // Index
            $table->index('mr_code');
            $table->index('visit_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
