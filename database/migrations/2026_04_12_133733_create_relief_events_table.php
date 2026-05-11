<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relief_events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->date('date');
            $table->string('venue', 255);
            $table->enum('status', ['Upcoming', 'Ongoing', 'Done'])->default('Upcoming');
            $table->foreignId('calamity_id')->nullable()->constrained('calamities')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relief_events');
    }
};