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
            // Add unique composite constraint on first_name, last_name, and birthdate
            $table->unique(['first_name', 'last_name', 'birthdate'], 'unique_beneficiary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('unique_beneficiary');
        });
    }
};
