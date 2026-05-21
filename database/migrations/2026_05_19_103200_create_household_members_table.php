<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'household_members';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->unsignedBigInteger('household_request_id');
        $table->string('name');
        $table->integer('age');
        $table->string('sex');
        $table->timestamps();
        $table->foreign('household_request_id')->references('id')->on('household_requests')->onDelete('cascade');
    }
};