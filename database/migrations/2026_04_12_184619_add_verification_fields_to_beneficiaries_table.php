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
        $table->boolean('has_senior')->default(false)->after('family_size');
        $table->integer('children_count')->default(0)->after('has_senior');
        $table->integer('criteria_met')->default(0)->after('children_count');
        $table->text('interview_notes')->nullable()->after('criteria_met');
        $table->foreignId('interviewed_by')->nullable()
        ->constrained('users')->onDelete('set null')
        ->after('interview_notes');
        $table->timestamp('interviewed_at')->nullable()->after('interviewed_by');
    }
};