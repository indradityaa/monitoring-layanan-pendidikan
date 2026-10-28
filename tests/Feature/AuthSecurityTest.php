<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_logout_with_session_cleanup(): void
    {
        $email = 'security-admin@example.com';

        RateLimiter::clear($email . '|127.0.0.1');

        $user = User::factory()->create([
            'name' => 'Admin Test',
            'email' => $email,
            'password' => Hash::make('CorrectPassword123!'),
            'role' => 'admin',
        ]);

        $this->post(route('login'), [
            'email' => $email,
            'password' => 'CorrectPassword123!',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        $email = 'failed-attempts@example.com';
        $throttleKey = strtolower($email) . '|127.0.0.1';

        RateLimiter::clear($throttleKey);

        // Lima percobaan gagal pertama tetap menerima pesan kesalahan login.
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from(route('login'))
                ->post(route('login'), [
                    'email' => $email,
                    'password' => 'WrongPassword123!',
                ])
                ->assertRedirect(route('login'))
                ->assertSessionHasErrors('email');
        }

        // Percobaan berikutnya harus dibatasi.
        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $email,
                'password' => 'WrongPassword123!',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertTrue(
            RateLimiter::tooManyAttempts($throttleKey, 5)
        );
    }
}
