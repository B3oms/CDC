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
        $table->id();
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->string('name', 100);
        $table->text('description')->nullable();
        $table->string('image', 255)->nullable();
        $table->timestamps();
    }
};