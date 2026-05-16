<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommended_beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('recommended_beneficiaries', 'gender')) {
                $table->enum('gender', ['Male', 'Female'])->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('recommended_beneficiaries', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('gender');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recommended_beneficiaries', function (Blueprint $table) {
            if (Schema::hasColumn('recommended_beneficiaries', 'age')) {
                $table->dropColumn('age');
            }
            if (Schema::hasColumn('recommended_beneficiaries', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
