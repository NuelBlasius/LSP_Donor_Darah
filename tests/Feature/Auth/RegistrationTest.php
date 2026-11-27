<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_new_donor_can_register_with_phone_and_is_pending_verification(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Donor',
            'nomor_telepon' => '081234567890',
            'nik' => '1671010101011234',
            'jenis_kelamin' => 'Laki-laki',
            'golongan_darah' => 'O+',
            'tanggal_lahir' => '2000-04-12',
            'alamat' => 'Jl. Contoh No. 10, Palembang',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'nomor_telepon' => '081234567890',
            'nik' => '1671010101011234',
            'golongan_darah' => 'O+',
            'role' => 'donor',
            'account_status' => 'pending',
        ]);
    }
}
