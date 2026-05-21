<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'categories';
    }

    protected function columns(Blueprint $table): void
    {
        $table->string('color', 7)->default('#10B981')->after('name'); // Default green color
    }
};