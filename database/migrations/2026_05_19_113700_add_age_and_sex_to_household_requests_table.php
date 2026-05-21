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
        $table->integer('head_age')->nullable()->after('head_of_household');
        $table->string('head_sex')->nullable()->after('head_age');
    }
};