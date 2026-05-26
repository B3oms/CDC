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
        $table->tinyInteger('is_indigenous')->nullable()->after('is_4ps_member')->comment('0=No, 1=Yes');
        $table->tinyInteger('is_pwd')->nullable()->after('is_indigenous')->comment('0=No, 1=Yes');
        $table->string('pwd_type')->nullable()->after('is_pwd')->comment('Type of disability');
    }
};