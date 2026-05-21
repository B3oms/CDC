<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'items';
    }

    protected function columns(Blueprint $table): void
    {
        $table->foreignId('subcategory_id')
        ->nullable()
        ->after('category_id')
        ->constrained('subcategories')
        ->onDelete('set null');
    };
};