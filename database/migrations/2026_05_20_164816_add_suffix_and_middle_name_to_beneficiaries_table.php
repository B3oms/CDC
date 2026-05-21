<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'beneficiaries';
    }

    protected function columns(Blueprint $table): void
    {
        $table->string('suffix')->nullable()->after('last_name');
        $table->string('middle_name')->nullable()->after('first_name');
    }
};