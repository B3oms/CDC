<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'relief_event_beneficiaries';
    }

    protected function columns($table): void
    {
        $table->id();
        $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
        $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
        $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
        $table->timestamps();
        // Shortened index name to avoid MySQL 64-char limit
        $table->unique(['relief_event_id', 'barangay_id', 'beneficiary_id'], 'reb_unique');
    }
};