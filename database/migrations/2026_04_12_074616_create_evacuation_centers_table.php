<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'evacuation_centers';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('calamity_id')->constrained('calamities')->onDelete('cascade');
        $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
        $table->string('venue', 255);
        $table->string('location', 255);
        $table->timestamps();
    };
};