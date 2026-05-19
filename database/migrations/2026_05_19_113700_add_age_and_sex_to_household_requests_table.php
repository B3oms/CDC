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
        Schema::table('household_requests', function (Blueprint $table) {
            $table->integer('head_age')->nullable()->after('head_of_household');
            $table->string('head_sex')->nullable()->after('head_age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('household_requests', function (Blueprint $table) {
            $table->dropColumn(['head_age', 'head_sex']);
        });
    }
};
