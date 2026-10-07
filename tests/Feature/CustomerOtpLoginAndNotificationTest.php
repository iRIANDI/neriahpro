<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\CustomerOtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomerOtpLoginAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Test OTP request with invalid email returns 422.
     */
    public function test_customer_otp_request_validates_email(): void
    {
        $response = $this->postJson('/api/customer/otp/request', [
            'email' => 'bukan-email-valid',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonStructure(['errors' => ['email']]);
    }

    /**
     * Test OTP request with valid email sends CustomerOtpMail with 6-digit code.
     */
    public function test_customer_otp_request_sends_email_with_6_digit_code(): void
    {
        Mail::fake();

        $email = 'founder@startup.co.id';
        $response = $this->postJson('/api/customer/otp/request', [
            'email' => $email,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'expires_in_minutes' => 10,
        ]);

        Mail::assertSent(CustomerOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email) 
                && strlen($mail->otp) === 6 
                && is_numeric($mail->otp);
        });

        // Ensure cached record exists
        $cacheKey = 'customer_otp_' . md5($email);
        $this->assertNotNull(Cache::get($cacheKey));
    }

    /**
     * Test OTP request rate limiting blocks after maximum requests in window.
     */
    public function test_customer_otp_request_rate_limiting(): void
    {
        Mail::fake();
        $email = 'rate.limit@test.com';

        // 3 allowed requests
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/customer/otp/request', ['email' => $email]);
            $response->assertStatus(200);
        }

        // 4th request must be throttled with 429
        $response = $this->postJson('/api/customer/otp/request', ['email' => $email]);
        $response->assertStatus(429);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Terlalu banyak permintaan OTP', $response->json('message'));
    }

    /**
     * Test OTP verification fails with wrong code and decrements remaining attempts.
     */
    public function test_customer_otp_verify_with_wrong_code_fails(): void
    {
        $email = 'wrong.code@test.com';
        $cacheKey = 'customer_otp_' . md5($email);

        Cache::put($cacheKey, [
            'hash' => bcrypt('123456'),
            'attempts' => 0,
            'created_at' => now()->timestamp,
            'ip' => '127.0.0.1',
        ], now()->addMinutes(10));

        $response = $this->postJson('/api/customer/otp/verify', [
            'email' => $email,
            'otp' => '999999',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'remaining_attempts' => 4,
        ]);

        $this->assertFalse(auth()->check());
    }

    /**
     * Test OTP verification fails with expired / non-existent code.
     */
    public function test_customer_otp_verify_with_expired_code_fails(): void
    {
        $response = $this->postJson('/api/customer/otp/verify', [
            'email' => 'expired@test.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('tidak ditemukan atau telah kedaluwarsa', $response->json('message'));
    }

    /**
     * Test OTP verification lockout after 5 consecutive failed attempts.
     */
    public function test_customer_otp_verify_lockout_after_max_attempts(): void
    {
        $email = 'lockout@test.com';
        $cacheKey = 'customer_otp_' . md5($email);

        Cache::put($cacheKey, [
            'hash' => bcrypt('123456'),
            'attempts' => 5, // Already reached 5
            'created_at' => now()->timestamp,
            'ip' => '127.0.0.1',
        ], now()->addMinutes(10));

        $response = $this->postJson('/api/customer/otp/verify', [
            'email' => $email,
            'otp' => '123456',
        ]);

        $response->assertStatus(429);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Terlalu banyak percobaan kode salah', $response->json('message'));

        // Cache must have been purged
        $this->assertNull(Cache::get($cacheKey));
    }

    /**
     * Test OTP verification succeeds with correct code, creates user, and logs in.
     */
    public function test_customer_otp_verify_success_authenticates_and_creates_user(): void
    {
        $email = 'client.verified@company.com';
        $code = '849201';
        $cacheKey = 'customer_otp_' . md5($email);

        Cache::put($cacheKey, [
            'hash' => bcrypt($code),
            'attempts' => 0,
            'created_at' => now()->timestamp,
            'ip' => '127.0.0.1',
        ], now()->addMinutes(10));

        $response = $this->postJson('/api/customer/otp/verify', [
            'email' => $email,
            'otp' => $code,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user' => [
                'email' => $email,
            ],
        ]);

        // Assert user authenticated in Laravel session
        $this->assertTrue(auth()->check());
        $this->assertEquals($email, auth()->user()->email);
        $this->assertNotNull(auth()->user()->email_verified_at);

        // Assert OTP removed from cache
        $this->assertNull(Cache::get($cacheKey));
    }

    /**
     * Test customer status and logout endpoints.
     */
    public function test_customer_status_and_logout(): void
    {
        $user = User::create([
            'name' => 'Active Customer',
            'email' => 'active@customer.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        // Status
        $statusResponse = $this->getJson('/api/customer/status');
        $statusResponse->assertStatus(200);
        $statusResponse->assertJson([
            'logged_in' => true,
            'user' => [
                'email' => 'active@customer.com',
            ],
        ]);

        // Logout
        $logoutResponse = $this->postJson('/api/customer/logout');
        $logoutResponse->assertStatus(200);
        $logoutResponse->assertJson(['success' => true]);

        $this->assertFalse(auth()->check());
    }

    /**
     * Test customer login page renders with subtle corners and clean layout.
     */
    public function test_customer_login_page_renders_cleanly(): void
    {
        $response = $this->get('/customer/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Portal Klien');
        $response->assertSee('Zero-Password');
        $response->assertSee('customer-email');
        $response->assertSee('otp-code');
    }
}
