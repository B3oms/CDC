<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'evacuation_reports';
    }

    protected function columns(Blueprint $table): void
    {
        $table->json('household_ids')->nullable()->after('household_count');
    }
};