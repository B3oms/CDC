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
            // Add age field
            $table->integer('age')->nullable()->after('gender');
            
            // Mother information
            $table->string('mother_name')->nullable()->after('age');
            $table->integer('mother_age')->nullable()->after('mother_name');
            $table->string('mother_sex')->nullable()->after('mother_age');
            $table->date('mother_birthdate')->nullable()->after('mother_sex');
            
            // Father information
            $table->string('father_name')->nullable()->after('mother_birthdate');
            $table->integer('father_age')->nullable()->after('father_name');
            $table->string('father_sex')->nullable()->after('father_age');
            $table->date('father_birthdate')->nullable()->after('father_sex');
            
            // Spouse information
            $table->string('spouse_name')->nullable()->after('father_birthdate');
            $table->integer('spouse_age')->nullable()->after('spouse_name');
            $table->string('spouse_sex')->nullable()->after('spouse_age');
            $table->date('spouse_birthdate')->nullable()->after('spouse_sex');
            $table->string('spouse_occupation')->nullable()->after('spouse_birthdate');
            
            // Children information (JSON field to store multiple children)
            $table->json('children')->nullable()->after('spouse_occupation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn([
                'age',
                'mother_name',
                'mother_age', 
                'mother_sex',
                'mother_birthdate',
                'father_name',
                'father_age',
                'father_sex',
                'father_birthdate',
                'spouse_name',
                'spouse_age',
                'spouse_sex',
                'spouse_birthdate',
                'spouse_occupation',
                'children'
            ]);
        });
    }
};
