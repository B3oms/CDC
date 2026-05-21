<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'evacuation_reports';
    }

    protected function columns(Blueprint $table): void
    {
        // Add barangay_id if it doesn't exist
        if (!Schema::hasColumn('evacuation_reports', 'barangay_id')) {
        $table->unsignedBigInteger('barangay_id')->nullable()->after('id');
        $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('cascade');
    }
};