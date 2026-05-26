<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'evacuation_reports';
    }

    protected function columns($table): void
    {
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
    }
};