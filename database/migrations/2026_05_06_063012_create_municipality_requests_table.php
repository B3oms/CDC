<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'municipality_requests';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
        $table->string('name');
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('remarks')->nullable();
        $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamp('approved_at')->nullable();
        $table->text('rejection_reason')->nullable();
        $table->timestamps();
        $table->index('status');
        $table->index('requested_by');
        $table->index('approved_by');
    };
};