<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'relief_event_barangays';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
        $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
        $table->foreignId('municipality_id')->constrained('municipalities')->onDelete('cascade');
        $table->timestamps();
        $table->unique(['relief_event_id', 'barangay_id']);
    };
};