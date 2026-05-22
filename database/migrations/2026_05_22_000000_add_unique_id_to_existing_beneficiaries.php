<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('beneficiaries') && !Schema::hasColumn('beneficiaries', 'unique_id')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->string('unique_id', 20)->unique()->after('id')->comment('Random unique beneficiary ID');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('beneficiaries') && Schema::hasColumn('beneficiaries', 'unique_id')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->dropColumn('unique_id');
            });
        }
    }
};
