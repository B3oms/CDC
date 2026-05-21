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
            $table->tinyInteger('is_indigenous')->nullable()->after('is_4ps_member')->comment('0=No, 1=Yes');
            $table->tinyInteger('is_pwd')->nullable()->after('is_indigenous')->comment('0=No, 1=Yes');
            $table->string('pwd_type')->nullable()->after('is_pwd')->comment('Type of disability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['is_indigenous', 'is_pwd', 'pwd_type']);
        });
    }
};
