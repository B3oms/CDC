<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relief_event_barangays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
            $table->foreignId('municipality_id')->constrained('municipalities')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['relief_event_id', 'barangay_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relief_event_barangays');
    }
};