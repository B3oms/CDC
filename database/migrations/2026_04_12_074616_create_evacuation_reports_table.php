<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evacuation_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evacuation_center_id')->constrained('evacuation_centers')->onDelete('cascade');
            $table->foreignId('calamity_id')->constrained('calamities')->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->integer('household_count')->default(0);
            $table->integer('evacuee_count')->default(0);
            $table->enum('severity_level', ['1', '2', '3', '4', '5'])->default('1');
            $table->decimal('ranking_score', 8, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evacuation_reports');
    }
};