<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('household_members', function (Blueprint $table) {
            // Drop the old foreign key if it exists
            $table->dropForeign(['household_request_id']);
            
            // Rename the column from household_request_id to household_id
            $table->renameColumn('household_request_id', 'household_id');
            
            // Add the new foreign key constraint
            $table->foreign('household_id')->constrained('households')->onDelete('cascade');
            
            // Add the missing relationship_to_head column
            $table->string('relationship_to_head')->after('sex');
        });
    }

    public function down(): void
    {
        Schema::table('household_members', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['household_id']);
            
            // Rename back to household_request_id
            $table->renameColumn('household_id', 'household_request_id');
            
            // Add back the old foreign key
            $table->foreign('household_request_id')->constrained('household_requests')->onDelete('cascade');
            
            // Drop the relationship_to_head column
            $table->dropColumn('relationship_to_head');
        });
    }
};
