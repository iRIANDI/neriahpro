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
        User::create([
            'name' => 'Startup Founder',
            'email' => $email,
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/customer/otp/request', [
            'email' => $email,
            'password' => 'secret123',
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

        User::create([
            'name' => 'Rate Limit Test',
            'email' => $email,
            'password' => bcrypt('secret123'),
        ]);

        // 5 allowed requests
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/customer/otp/request', ['email' => $email, 'password' => 'secret123']);
            $response->assertStatus(200);
        }

        // 6th request must be throttled with 429
        $response = $this->postJson('/api/customer/otp/request', ['email' => $email, 'password' => 'secret123']);
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
        $response->assertSee('2-FA');
        $response->assertSee('customer-email');
        $response->assertSee('customer-password');
        $response->assertSee('otp-code');
    }

    /**
     * Test login with wrong password fails before dispatching OTP.
     */
    public function test_customer_login_with_wrong_password_fails(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Existing Customer',
            'email' => 'existing@customer.com',
            'password' => bcrypt('correctPassword123'),
        ]);

        $response = $this->postJson('/api/customer/otp/request', [
            'email' => 'existing@customer.com',
            'password' => 'wrongPassword',
            'mode' => 'login',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Kata sandi yang Anda masukkan salah', $response->json('message'));

        Mail::assertNothingSent();
    }

    /**
     * Test login with correct password sends OTP and allows subsequent verification.
     */
    public function test_customer_login_with_correct_password_sends_otp(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Existing Customer',
            'email' => 'existing2@customer.com',
            'password' => bcrypt('correctPassword123'),
        ]);

        $response = $this->postJson('/api/customer/otp/request', [
            'email' => 'existing2@customer.com',
            'password' => 'correctPassword123',
            'mode' => 'login',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'mode' => 'login',
        ]);

        Mail::assertSent(CustomerOtpMail::class, function ($mail) {
            return $mail->hasTo('existing2@customer.com') && strlen($mail->otp) === 6;
        });
    }

    /**
     * Test customer dashboard renders cleanly for authenticated user.
     */
    public function test_customer_dashboard_renders_cleanly(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->get('/customer/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Neriah');
        $response->assertSee('john@doe.com');
    }

    /**
     * Test login request fails when captcha_verified is false.
     */
    public function test_customer_login_fails_when_captcha_not_verified(): void
    {
        $email = 'captcha.test@customer.com';
        User::create([
            'name' => 'Captcha User',
            'email' => $email,
            'password' => bcrypt('securePassword123'),
        ]);

        $response = $this->postJson('/api/customer/otp/request', [
            'email' => $email,
            'password' => 'securePassword123',
            'mode' => 'login',
            'captcha_verified' => false,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Captcha', $response->json('message'));
    }

    /**
     * Test login request succeeds when captcha_verified is true and password is correct.
     */
    public function test_customer_login_succeeds_when_captcha_verified_and_password_correct(): void
    {
        Mail::fake();

        $email = 'captcha.success@customer.com';
        User::create([
            'name' => 'Captcha Success User',
            'email' => $email,
            'password' => bcrypt('securePassword123'),
        ]);

        $response = $this->postJson('/api/customer/otp/request', [
            'email' => $email,
            'password' => 'securePassword123',
            'mode' => 'login',
            'captcha_verified' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'mode' => 'login',
        ]);

        Mail::assertSent(CustomerOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email) && strlen($mail->otp) === 6;
        });
    }

    /**
     * Test customer login page renders with visual scatter pinpoint captcha modal.
     */
    public function test_customer_login_page_renders_with_visual_scatter_captcha_modal(): void
    {
        $response = $this->get('/customer/login');

        $response->assertStatus(200);
        $response->assertSee('Pin Objek Gambar (Visual Captcha)');
        $response->assertSee('scatteredSceneObjects');
        $response->assertSee('PINPOINT OBJEK GAMBAR');
        $response->assertSee('PIN #');
    }

    /**
     * Test customer OTP mail renders using support@neriahpro.com and dynamic email template.
     */
    public function test_customer_otp_mail_uses_dynamic_template_and_support_email(): void
    {
        $mailable = new CustomerOtpMail(
            otp: '308933',
            email: 'client@company.com',
            ipAddress: '162.159.98.106',
            expiryMinutes: 10
        );

        $rendered = $mailable->render();

        $this->assertStringContainsString('308933', $rendered);
        $this->assertStringContainsString('client@company.com', $rendered);
        $this->assertStringContainsString('support@neriahpro.com', $rendered);
        $this->assertStringNotContainsString('yoseph.iriandi.tambunan@gmail.com', $rendered);
        $this->assertStringContainsString('162.159.98.106', $rendered);
        $this->assertStringContainsString('Autentikasi Klien // Zero-Password', $rendered);
    }
}
