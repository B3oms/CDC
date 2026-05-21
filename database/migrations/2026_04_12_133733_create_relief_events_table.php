<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'relief_events';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->string('name', 150);
        $table->date('date');
        $table->string('venue', 255);
        $table->enum('status', ['Upcoming', 'Ongoing', 'Done'])->default('Upcoming');
        $table->foreignId('calamity_id')->nullable()->constrained('calamities')->onDelete('set null');
        $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamps();
    }
};