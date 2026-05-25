<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relief_events', function (Blueprint $table) {
            $table->string('calamity_type', 100)->nullable()->after('calamity_id');
        });
    }

    public function down(): void
    {
        Schema::table('relief_events', function (Blueprint $table) {
            $table->dropColumn('calamity_type');
        });
    }
};
