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
            // Drop the old boolean field
            $table->dropColumn('is_indigenous');
            // Add new string field for indigenous group
            $table->string('indigenous_group')->nullable()->after('is_4ps_member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            // Drop the new string field
            $table->dropColumn('indigenous_group');
            // Restore the old boolean field
            $table->tinyInteger('is_indigenous')->nullable()->after('is_4ps_member')->comment('0=No, 1=Yes');
        });
    }
};
