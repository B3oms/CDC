<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'beneficiaries';
    }

    protected function columns(Blueprint $table): void
    {
        $table->string('unique_id')->nullable()->after('barangay_id');
    };
};