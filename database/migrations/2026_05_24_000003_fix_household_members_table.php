<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_members', function (Blueprint $table) {
            // Check if the column exists before proceeding
            if (Schema::hasColumn('household_members', 'household_request_id')) {
                // Drop the old foreign key if it exists
                try {
                    $table->dropForeign(['household_request_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                
                // Rename the column from household_request_id to household_id
                $table->renameColumn('household_request_id', 'household_id');
                
                // Add the new foreign key constraint
                $table->foreign('household_id')->constrained('households')->onDelete('cascade');
            }
            
            // Add the missing relationship_to_head column if it doesn't exist
            if (!Schema::hasColumn('household_members', 'relationship_to_head')) {
                $table->string('relationship_to_head')->after('sex');
            }
        });
    }

    public function down(): void
    {
        Schema::table('household_members', function (Blueprint $table) {
            // Check if the household_id column exists before proceeding
            if (Schema::hasColumn('household_members', 'household_id')) {
                // Drop the foreign key if it exists
                try {
                    $table->dropForeign(['household_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                
                // Rename back to household_request_id
                $table->renameColumn('household_id', 'household_request_id');
                
                // Add back the old foreign key if the table exists
                if (Schema::hasTable('household_requests')) {
                    try {
                        $table->foreign('household_request_id')->constrained('household_requests')->onDelete('cascade');
                    } catch (\Exception $e) {
                        // Table or constraint doesn't exist, continue
                    }
                }
            }
            
            // Drop the relationship_to_head column if it exists
            if (Schema::hasColumn('household_members', 'relationship_to_head')) {
                $table->dropColumn('relationship_to_head');
            }
        });
    }
};
