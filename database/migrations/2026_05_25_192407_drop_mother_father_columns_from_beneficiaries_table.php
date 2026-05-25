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
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn([
                'mother_name',
                'mother_age',
                'mother_sex',
                'mother_birthdate',
                'mother_deceased',
                'father_name',
                'father_age',
                'father_sex',
                'father_birthdate',
                'father_deceased',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('mother_name')->nullable();
            $table->integer('mother_age')->nullable();
            $table->string('mother_sex')->nullable();
            $table->date('mother_birthdate')->nullable();
            $table->boolean('mother_deceased')->default(false);
            $table->string('father_name')->nullable();
            $table->integer('father_age')->nullable();
            $table->string('father_sex')->nullable();
            $table->date('father_birthdate')->nullable();
            $table->boolean('father_deceased')->default(false);
        });
    }
};
