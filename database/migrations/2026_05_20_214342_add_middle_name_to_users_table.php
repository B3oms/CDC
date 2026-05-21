<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'users';
    }

    protected function columns(Blueprint $table): void
    {
        $table->string('middle_name')->nullable()->after('first_name');
    }
};