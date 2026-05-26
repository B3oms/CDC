<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'location_requests';
    }

    protected function columns($table): void
    {
        $table->string('province')->nullable()->after('name');
    }
};