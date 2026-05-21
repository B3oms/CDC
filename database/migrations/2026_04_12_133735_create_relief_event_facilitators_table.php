<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'relief_event_facilitators';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->timestamps();
        $table->unique(['relief_event_id', 'user_id']);
    }
};