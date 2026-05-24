<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop existing tables
        Schema::dropIfExists('household_members');
        Schema::dropIfExists('households');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Create households table with correct structure
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

        // Create household_members table with correct structure
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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Schema::dropIfExists('household_members');
        Schema::dropIfExists('households');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
