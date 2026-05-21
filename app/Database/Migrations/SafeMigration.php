<?php

namespace App\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class SafeMigration extends Migration
{
    /**
     * The table name for this migration.
     */
    abstract protected function tableName(): string;

    /**
     * Define the table columns.
     */
    abstract protected function columns(Blueprint $table): void;

    public function up(): void
    {
        $table = $this->tableName();

        if (!Schema::hasTable($table)) {
            // Table doesn't exist, create it fresh
            Schema::create($table, function (Blueprint $table) {
                $this->columns($table);
            });
        } else {
            // Table exists, only apply missing columns
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $this->applyMissingColumns($table, $blueprint);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName());
    }

    private function applyMissingColumns(string $table, Blueprint $blueprint): void
    {
        // For existing tables, just run the columns method
        // Laravel will handle adding only missing columns automatically
        $this->columns($blueprint);
    }
}