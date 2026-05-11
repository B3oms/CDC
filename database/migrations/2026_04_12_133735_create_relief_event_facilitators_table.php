<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relief_event_facilitators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['relief_event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relief_event_facilitators');
    }
};