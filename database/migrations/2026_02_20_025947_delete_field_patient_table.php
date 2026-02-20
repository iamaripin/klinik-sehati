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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('patient_card_number')->nullable()->after('patient_nik');
            $table->string('patient_contact')->nullable()->after('patient_dob');
            $table->string('patient_relation_name')->nullable()->after('patient_alergy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'patient_phone',
                'patient_email',
                'patient_special',
                'insurance_type',
                'insurance_number',
                'insurance_code',
                'insurance_company',
                'insurance_valid_until'
            ]);
        });
    }
};
