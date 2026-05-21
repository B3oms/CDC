<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'municipalities';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->string('name');
        $table->string('province');
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('notes')->nullable();
        $table->timestamps();
    };
};