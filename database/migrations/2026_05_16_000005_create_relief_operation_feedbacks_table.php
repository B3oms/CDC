<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('relief_operation_feedbacks')) {
            Schema::create('relief_operation_feedbacks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('relief_operation_id')->constrained('relief_operations')->onDelete('cascade');
                $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
                $table->text('message');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('relief_operation_feedbacks');
    }
};
