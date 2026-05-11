<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relief_event_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
            $table->timestamps();

            // Shortened index name to avoid MySQL 64-char limit
            $table->unique(['relief_event_id', 'barangay_id', 'beneficiary_id'], 'reb_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relief_event_beneficiaries');
    }
};