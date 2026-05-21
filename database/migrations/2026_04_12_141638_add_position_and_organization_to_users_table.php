<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'users';
    }

    protected function columns(Blueprint $table): void
    {
        $table->string('position', 100)->nullable()->after('contact_number');
        $table->string('organization', 150)->nullable()->after('position');
        $table->date('birthdate')->nullable()->after('organization');
    };
};