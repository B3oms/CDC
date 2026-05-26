<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'beneficiaries';
    }

    protected function columns($table): void
    {
        $table->boolean('is_4ps_member')->default(false)->after('gender');
    }
};