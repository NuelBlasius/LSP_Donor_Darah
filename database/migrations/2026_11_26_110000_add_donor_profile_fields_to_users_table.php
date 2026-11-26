<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 16)->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'golongan_darah')) {
                $table->string('golongan_darah', 3)->nullable();
            }
            if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable();
            }
            if (!Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['nik', 'jenis_kelamin', 'golongan_darah', 'tanggal_lahir', 'alamat'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
