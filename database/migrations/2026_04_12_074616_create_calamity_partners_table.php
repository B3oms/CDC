<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    if (!Schema::hasTable('calamity_partners')) {
        Schema::create('calamity_partners', function (Blueprint $table) {
            // ...
        });
    }
}

    public function down(): void
    {
        Schema::dropIfExists('calamity_partners');
    }
};