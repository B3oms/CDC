<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key constraints first
        if (Schema::hasTable('submission_evacuees')) {
            try {
                Schema::table('submission_evacuees', function (Blueprint $table) {
                    $table->dropForeign('fk_se_household');
                });
            } catch (\Exception $e) {
                // Foreign key doesn't exist, continue
            }
        }
        
        // Drop existing tables
        Schema::dropIfExists('household_members');
        Schema::dropIfExists('households');
        
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
        // Drop foreign key constraints first
        if (Schema::hasTable('submission_evacuees')) {
            try {
                Schema::table('submission_evacuees', function (Blueprint $table) {
                    $table->dropForeign('fk_se_household');
                });
            } catch (\Exception $e) {
                // Foreign key doesn't exist, continue
            }
        }
        
        // Drop existing tables
        Schema::dropIfExists('household_members');
        Schema::dropIfExists('households');
    }
};
