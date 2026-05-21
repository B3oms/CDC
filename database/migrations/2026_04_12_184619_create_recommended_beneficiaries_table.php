<?php

use App\Database\Migrations\SafeMigration;
use Illuminate\Database\Schema\Blueprint;

return new class extends SafeMigration
{
    protected function tableName(): string
    {
        return 'recommended_beneficiaries';
    }

    protected function columns(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
        $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
        $table->string('first_name', 100);
        $table->string('last_name', 100);
        $table->string('contact_number', 13)->nullable();
        $table->text('address')->nullable();
        $table->enum('status', ['Pending', 'Converted', 'Rejected'])->default('Pending');
        $table->timestamps();
    }
};