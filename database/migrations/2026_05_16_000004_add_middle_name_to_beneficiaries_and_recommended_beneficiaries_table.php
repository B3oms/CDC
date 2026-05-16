<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommended_beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('recommended_beneficiaries', 'middle_name')) {
                $table->string('middle_name', 100)->nullable()->after('first_name');
            }
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiaries', 'middle_name')) {
                $table->string('middle_name', 100)->nullable()->after('first_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recommended_beneficiaries', function (Blueprint $table) {
            if (Schema::hasColumn('recommended_beneficiaries', 'middle_name')) {
                $table->dropColumn('middle_name');
            }
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiaries', 'middle_name')) {
                $table->dropColumn('middle_name');
            }
        });
    }
};
