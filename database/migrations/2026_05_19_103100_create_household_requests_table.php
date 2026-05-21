<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'household_requests';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->unsignedBigInteger('barangay_id');
        $table->string('head_name');
        $table->integer('head_age');
        $table->string('head_sex');
        $table->date('head_date_of_birth');
        $table->text('address');
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('rejection_reason')->nullable();
        $table->unsignedBigInteger('approved_by')->nullable();
        $table->timestamp('approved_at')->nullable();
        $table->text('staff_notes')->nullable();
        $table->timestamps();
        $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('cascade');
        $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
    }
};