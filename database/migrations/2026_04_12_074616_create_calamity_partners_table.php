<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'calamity_partners';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('calamity_id')->constrained()->cascadeOnDelete();
        $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
    }
};