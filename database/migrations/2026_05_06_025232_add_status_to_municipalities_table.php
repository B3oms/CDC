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
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('province');
        $table->text('notes')->nullable()->after('status');
    };
};