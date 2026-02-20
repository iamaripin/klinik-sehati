<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('general_queue', function (Blueprint $table) {
            $table->id();

            $table->string('mr_code');
            $table->string('visit_no');
            $table->date('visit_date');

            $table->string('doctor_code');
            $table->integer('queue_no');
 
            $table->string('poli')->nullable();
            $table->enum('queue_status', ['WAITING', 'CALL', 'DONE'])->default('WAITING');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();

            $table->index('visit_no');
            $table->index('doctor_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_queue');
    }
};
