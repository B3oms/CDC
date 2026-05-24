<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('household_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->string('name');
            $table->integer('age');
            $table->string('sex');
            $table->string('relationship_to_head');
            $table->timestamps();
            
            $table->index('household_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_members');
    }
};
