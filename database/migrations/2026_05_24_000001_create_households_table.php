<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('households')) {
            Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('head_of_household');
            $table->integer('age');
            $table->string('sex');
            $table->date('birthdate');
            $table->string('contact_number')->nullable();
            $table->boolean('is_cdc_beneficiary')->default(false);
            $table->text('address');
            $table->string('status')->default('active');
            $table->timestamps();
            
            $table->index(['barangay_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
