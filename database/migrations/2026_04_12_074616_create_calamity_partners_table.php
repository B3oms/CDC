<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calamity_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calamity_id')->constrained('calamities')->onDelete('cascade');
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['calamity_id', 'barangay_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calamity_partners');
    }
};