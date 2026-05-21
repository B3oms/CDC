<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'beneficiaries';
    }

    protected function columns(Blueprint $table): void
    {
        // Add unique composite constraint on first_name, last_name, and birthdate
        $table->unique(['first_name', 'last_name', 'birthdate'], 'unique_beneficiary');
    }
};