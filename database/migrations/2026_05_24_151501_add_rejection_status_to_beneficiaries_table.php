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
            // Add status field to track beneficiary status
            $table->enum('status', ['verified', 'rejected', 'pending'])->default('pending')->after('is_verified');
            
            // Add rejection date to track when beneficiary was rejected
            $table->date('rejection_date')->nullable()->after('status');
            
            // Add scheduled deletion date
            $table->date('scheduled_deletion_date')->nullable()->after('rejection_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejection_date', 'scheduled_deletion_date']);
        });
    }
};
