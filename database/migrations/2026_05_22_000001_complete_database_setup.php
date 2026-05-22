<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create all missing tables and columns
        $this->createMissingTables();
        $this->addMissingColumns();
    }

    private function createMissingTables(): void
    {
        // List of tables that should exist
        $requiredTables = [
            'calamity_partners',
            'evacuation_centers',
            'evacuation_reports',
            'relief_events',
            'relief_event_barangays',
            'relief_event_facilitators',
            'relief_event_beneficiaries',
            'subcategories',
            'recommended_beneficiaries',
            'municipalities',
            'municipality_requests',
            'barangay_requests',
            'location_requests',
            'notifications',
            'household_requests',
            'household_members',
        ];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->createTable($table);
            }
        }
    }

    private function createTable(string $tableName): void
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            
            switch ($tableName) {
                case 'calamity_partners':
                    $table->string('partner_name');
                    $table->string('contact_person');
                    $table->string('contact_number');
                    $table->string('email')->nullable();
                    $table->timestamps();
                    break;

                case 'evacuation_centers':
                    $table->foreignId('calamity_id')->constrained('calamities')->onDelete('cascade');
                    $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
                    $table->string('venue', 255);
                    $table->string('location', 255);
                    $table->timestamps();
                    break;

                case 'evacuation_reports':
                    $table->foreignId('evacuation_center_id')->constrained('evacuation_centers')->onDelete('cascade');
                    $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
                    $table->date('report_date');
                    $table->integer('families_count');
                    $table->integer('individuals_count');
                    $table->text('needs')->nullable();
                    $table->timestamps();
                    break;

                case 'relief_events':
                    $table->string('event_name');
                    $table->date('event_date');
                    $table->string('venue');
                    $table->foreignId('calamity_id')->constrained('calamities')->onDelete('cascade');
                    $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
                    $table->text('description')->nullable();
                    $table->timestamps();
                    break;

                case 'relief_event_barangays':
                    $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
                    $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
                    $table->timestamps();
                    break;

                case 'relief_event_facilitators':
                    $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
                    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                    $table->string('role');
                    $table->timestamps();
                    break;

                case 'relief_event_beneficiaries':
                    $table->foreignId('relief_event_id')->constrained('relief_events')->onDelete('cascade');
                    $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
                    $table->date('distribution_date')->nullable();
                    $table->string('status')->default('pending');
                    $table->timestamps();
                    break;

                case 'subcategories':
                    $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                    $table->string('name');
                    $table->string('description')->nullable();
                    $table->timestamps();
                    break;

                case 'recommended_beneficiaries':
                    $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
                    $table->foreignId('recommended_by')->constrained('users')->onDelete('cascade');
                    $table->text('reason');
                    $table->date('recommendation_date');
                    $table->string('status')->default('pending');
                    $table->timestamps();
                    break;

                case 'municipalities':
                    $table->string('name');
                    $table->string('province');
                    $table->string('status')->default('active');
                    $table->timestamps();
                    break;

                case 'municipality_requests':
                    $table->foreignId('municipality_id')->constrained('municipalities')->onDelete('cascade');
                    $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
                    $table->string('request_type');
                    $table->text('details');
                    $table->string('status')->default('pending');
                    $table->timestamps();
                    break;

                case 'barangay_requests':
                    $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
                    $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
                    $table->string('request_type');
                    $table->text('details');
                    $table->string('status')->default('pending');
                    $table->timestamps();
                    break;

                case 'location_requests':
                    $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
                    $table->string('location_type');
                    $table->string('name');
                    $table->string('region');
                    $table->string('province');
                    $table->string('municipality');
                    $table->string('barangay');
                    $table->text('details')->nullable();
                    $table->string('status')->default('pending');
                    $table->timestamps();
                    break;

                case 'notifications':
                    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                    $table->string('title');
                    $table->text('message');
                    $table->string('type');
                    $table->boolean('read')->default(false);
                    $table->timestamps();
                    break;

                case 'household_requests':
                    $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
                    $table->string('household_head');
                    $table->string('contact_number')->nullable();
                    $table->integer('family_members');
                    $table->string('age_group');
                    $table->string('sex');
                    $table->string('status')->default('pending');
                    $table->timestamps();
                    break;

                case 'household_members':
                    $table->foreignId('household_request_id')->constrained('household_requests')->onDelete('cascade');
                    $table->string('name');
                    $table->string('relationship');
                    $table->integer('age');
                    $table->string('sex');
                    $table->timestamps();
                    break;
            }
        });
    }

    private function addMissingColumns(): void
    {
        // Add missing columns to existing tables
        $this->addMissingUserColumns();
        $this->addMissingBeneficiaryColumns();
        $this->addMissingItemColumns();
        $this->addMissingCategoryColumns();
        $this->addMissingSubcategoryColumns();
    }

    private function addMissingUserColumns(): void
    {
        if (Schema::hasTable('users')) {
            $columns = Schema::getColumnListing('users');
            
            if (!in_array('position', $columns)) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('position')->nullable();
                });
            }
            
            if (!in_array('organization', $columns)) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('organization')->nullable();
                });
            }
            
            if (!in_array('suffix', $columns)) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('suffix')->nullable();
                });
            }
            
            if (!in_array('middle_name', $columns)) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('middle_name')->nullable();
                });
            }
        }
    }

    private function addMissingBeneficiaryColumns(): void
    {
        if (Schema::hasTable('beneficiaries')) {
            $columns = Schema::getColumnListing('beneficiaries');
            
            if (!in_array('unique_id', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->string('unique_id', 20)->unique()->after('id')->comment('Random unique beneficiary ID');
                });
            }
            
            if (!in_array('verification_status', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->string('verification_status')->default('pending');
                });
            }
            
            if (!in_array('verified_by', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
                });
            }
            
            if (!in_array('verified_at', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->timestamp('verified_at')->nullable();
                });
            }
            
            if (!in_array('suffix', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->string('suffix')->nullable()->after('last_name');
                });
            }
            
            if (!in_array('middle_name', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->string('middle_name')->nullable()->after('first_name');
                });
            }
            
            if (!in_array('is_4ps', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->boolean('is_4ps')->default(false);
                });
            }
            
            if (!in_array('gender', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->string('gender')->nullable();
                });
            }
            
            if (!in_array('is_indigenous', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->boolean('is_indigenous')->default(false);
                });
            }
            
            if (!in_array('is_pwd', $columns)) {
                Schema::table('beneficiaries', function (Blueprint $table) {
                    $table->boolean('is_pwd')->default(false);
                });
            }
        }
    }

    private function addMissingItemColumns(): void
    {
        if (Schema::hasTable('items')) {
            $columns = Schema::getColumnListing('items');
            
            if (!in_array('image', $columns)) {
                Schema::table('items', function (Blueprint $table) {
                    $table->string('image')->nullable();
                });
            }
            
            if (!in_array('description', $columns)) {
                Schema::table('items', function (Blueprint $table) {
                    $table->text('description')->nullable();
                });
            }
            
            if (!in_array('subcategory_id', $columns)) {
                Schema::table('items', function (Blueprint $table) {
                    $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->onDelete('set null');
                });
            }
            
            if (!in_array('color', $columns)) {
                Schema::table('items', function (Blueprint $table) {
                    $table->string('color')->nullable();
                });
            }
        }
    }

    private function addMissingCategoryColumns(): void
    {
        if (Schema::hasTable('categories')) {
            $columns = Schema::getColumnListing('categories');
            
            if (!in_array('image', $columns)) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->string('image')->nullable();
                });
            }
            
            if (!in_array('color', $columns)) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->string('color')->nullable();
                });
            }
        }
    }

    private function addMissingSubcategoryColumns(): void
    {
        if (Schema::hasTable('subcategories')) {
            $columns = Schema::getColumnListing('subcategories');
            
            if (!in_array('color', $columns)) {
                Schema::table('subcategories', function (Blueprint $table) {
                    $table->string('color')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        // This migration is meant to be one-way setup
        // Don't drop anything in production
    }
};
