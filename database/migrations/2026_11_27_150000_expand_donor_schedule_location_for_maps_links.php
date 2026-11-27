<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donor_schedules', function (Blueprint $table) {
            $table->string('location', 2048)->change();
        });
    }

    public function down(): void
    {
        Schema::table('donor_schedules', function (Blueprint $table) {
            $table->string('location', 2048)->change();
        });
    }
};
