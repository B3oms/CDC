<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('evacuation_reports', function (Blueprint $table) {
            $table->json('household_ids')->nullable()->after('household_count');
        });
    }

    public function down()
    {
        Schema::table('evacuation_reports', function (Blueprint $table) {
            $table->dropColumn('household_ids');
        });
    }
};
