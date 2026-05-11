<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->boolean('has_senior')->default(false)->after('family_size');
            $table->integer('children_count')->default(0)->after('has_senior');
            $table->integer('criteria_met')->default(0)->after('children_count');
            $table->text('interview_notes')->nullable()->after('criteria_met');
            $table->foreignId('interviewed_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->after('interview_notes');
            $table->timestamp('interviewed_at')->nullable()->after('interviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropForeign(['interviewed_by']);
            $table->dropColumn([
                'has_senior', 'children_count', 'criteria_met',
                'interview_notes', 'interviewed_by', 'interviewed_at'
            ]);
        });
    }
};