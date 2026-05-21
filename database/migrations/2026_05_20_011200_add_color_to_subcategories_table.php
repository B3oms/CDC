<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'subcategories';
    }

    protected function columns(Blueprint $table): void
    {
        $table->string('color', 7)->default('#3B82F6')->after('name'); // Default blue color
    };
};