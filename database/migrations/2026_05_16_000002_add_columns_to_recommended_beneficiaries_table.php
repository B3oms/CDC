<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommended_beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('recommended_beneficiaries', 'first_name')) {
                $table->string('first_name', 100)->nullable()->after('submitted_by');
            }
            if (!Schema::hasColumn('recommended_beneficiaries', 'last_name')) {
                $table->string('last_name', 100)->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('recommended_beneficiaries', 'contact_number')) {
                $table->string('contact_number', 13)->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('recommended_beneficiaries', 'address')) {
                $table->text('address')->nullable()->after('contact_number');
            }
            if (!Schema::hasColumn('recommended_beneficiaries', 'status')) {
                $table->enum('status', ['Pending', 'Converted', 'Rejected'])->default('Pending')->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recommended_beneficiaries', function (Blueprint $table) {
            if (Schema::hasColumn('recommended_beneficiaries', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('recommended_beneficiaries', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('recommended_beneficiaries', 'contact_number')) {
                $table->dropColumn('contact_number');
            }
            if (Schema::hasColumn('recommended_beneficiaries', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('recommended_beneficiaries', 'first_name')) {
                $table->dropColumn('first_name');
            }
        });
    }
};
