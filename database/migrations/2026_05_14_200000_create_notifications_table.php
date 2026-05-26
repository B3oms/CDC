<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'notifications';
    }

    protected function columns($table): void
    {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('type'); // portal_open, barangay_report, inventory_addition, beneficiary_addition, event_creation, location_request_approved, location_request_rejected
        $table->string('title');
        $table->text('message');
        $table->string('related_type')->nullable(); // portal, barangay, inventory, beneficiary, event, location_request
        $table->unsignedBigInteger('related_id')->nullable();
        $table->boolean('read')->default(false);
        $table->timestamps();
        $table->index(['user_id', 'read']);
        $table->index(['type', 'created_at']);
    }
};