<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('location_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['municipality', 'barangay']);
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->onDelete('cascade');
            $table->string('name');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'type']);
            $table->index('requested_by');
            $table->index('approved_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('location_requests');
    }
};
