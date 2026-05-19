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
        Schema::table('evacuation_reports', function (Blueprint $table) {
            // Add barangay_id if it doesn't exist
            if (!Schema::hasColumn('evacuation_reports', 'barangay_id')) {
                $table->unsignedBigInteger('barangay_id')->nullable()->after('id');
                $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('cascade');
            }
            
            // Add household_count if it doesn't exist
            if (!Schema::hasColumn('evacuation_reports', 'household_count')) {
                $table->integer('household_count')->default(0)->after('evacuee_count');
            }
            
            // Add severity_level if it doesn't exist
            if (!Schema::hasColumn('evacuation_reports', 'severity_level')) {
                $table->integer('severity_level')->nullable()->after('household_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evacuation_reports', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn(['barangay_id', 'household_count', 'severity_level']);
        });
    }
};
