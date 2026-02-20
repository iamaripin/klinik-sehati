<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->string('title')->nullable()->after('name');
            $table->string('suffix')->nullable()->after('title');
            $table->date('user_dob')->nullable()->after('suffix');
            $table->enum('gender', ['male', 'female'])->nullable()->after('user_dob');
            $table->enum('role', ['dev', 'admin', 'doctor', 'nurse', 'pharmacy'])
                ->default('doctor')
                ->after('gender');
            $table->enum('status', ['active', 'non active'])->default('active');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'title',
                'suffix',
                'user_dob',
                'gender',
                'role'
            ]);
        });
    }
};
