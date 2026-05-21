<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'household_requests';
    }

    protected function columns(Blueprint $table): void
    {
        $table->string('contact_number', 191)->nullable()->change();
    };
};