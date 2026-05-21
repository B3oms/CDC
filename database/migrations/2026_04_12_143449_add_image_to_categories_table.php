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
        $table->string('image', 255)->nullable()->after('description');
    }
};