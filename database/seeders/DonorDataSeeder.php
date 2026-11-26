<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DonorDataSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat akun admin
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@donor.test'],
            [
                'name' => 'Admin Donor',
                'nomor_telepon' => '081200000001',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'account_status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Membuat akun donor
        DB::table('users')->updateOrInsert(
            ['email' => 'andi@donor.test'],
            [
                'name' => 'Andi Saputra',
                'nomor_telepon' => '081200000002',
                'password' => Hash::make('donor12345'),
                'role' => 'donor',
                'account_status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'siti@donor.test'],
            [
                'name' => 'Siti Amelia',
                'nomor_telepon' => '081200000003',
                'password' => Hash::make('donor12345'),
                'role' => 'donor',
                'account_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Membuat jadwal donor
        DB::table('donor_schedules')->updateOrInsert(
            ['title' => 'Donor Darah Kampus'],
            [
                'location' => 'Aula Kampus',
                'schedule_date' => '2026-12-10',
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'quota' => 100,
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('donor_schedules')->updateOrInsert(
            ['title' => 'Donor Darah Sehat'],
            [
                'location' => 'Puskesmas Kota',
                'schedule_date' => '2026-12-15',
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'quota' => 50,
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Mengambil ID admin
        $adminId = DB::table('users')
            ->where('email', 'admin@donor.test')
            ->value('id');

        // Membuat pengumuman
        DB::table('announcements')->updateOrInsert(
            ['title' => 'Jadwal Donor Darah Desember'],
            [
                'admin_id' => $adminId,
                'content' => 'Donor darah akan dilaksanakan pada 10 Desember 2026 di Aula Kampus.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('announcements')->updateOrInsert(
            ['title' => 'Persyaratan Donor'],
            [
                'admin_id' => $adminId,
                'content' => 'Calon donor diharapkan membawa kartu identitas dan berada dalam kondisi sehat.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
