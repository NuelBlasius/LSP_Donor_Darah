<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donor_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('amount')->default(0)->after('quota');
        });

        Schema::table('donor_registrations', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->timestamp('payment_expires_at')->nullable()->after('payment_reference');
        });
    }

    public function down(): void
    {
        Schema::table('donor_registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_reference', 'payment_expires_at']);
        });

        Schema::table('donor_schedules', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
