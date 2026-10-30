<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_factor_service_generates_valid_totp_secret_and_qr(): void
    {
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecretKey();

        $this->assertNotEmpty($secret);
        $this->assertEquals(16, strlen($secret));

        $otpUri = $service->getOtpAuthUri('Neriah Pro', 'test@neriahpro.com', $secret);
        $this->assertStringContainsString('otpauth://totp/', $otpUri);

        $qrData = $service->getQrCodeUrl($otpUri);
        $this->assertNotEmpty($qrData);

        $recoveryCodes = $service->generateRecoveryCodes(8);
        $this->assertCount(8, $recoveryCodes);
    }

    public function test_user_can_enable_and_verify_two_factor_authentication(): void
    {
        $service = app(TwoFactorService::class);
        $user = User::factory()->create();

        $this->assertFalse($user->hasEnabledTwoFactor());

        $secret = $service->generateSecretKey();
        $recoveryCodes = $service->generateRecoveryCodes(8);

        $user->enableTwoFactor($secret, $recoveryCodes);

        $this->assertTrue($user->fresh()->hasEnabledTwoFactor());
        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);

        // Calculate valid TOTP for current time slice
        $currentSlice = (int) floor(time() / 30);
        $validCode = $service->calculateCode($secret, $currentSlice);
        $this->assertTrue($user->fresh()->verifyTwoFactorCode($validCode));

        // Invalid code should fail
        $this->assertFalse($user->fresh()->verifyTwoFactorCode('000000'));

        // Recovery code can be used once
        $firstRecovery = $recoveryCodes[0];
        $this->assertTrue($user->fresh()->verifyTwoFactorCode($firstRecovery));

        // Second use of the same recovery code should fail
        $this->assertFalse($user->fresh()->verifyTwoFactorCode($firstRecovery));

        // Disable 2FA
        $user->disableTwoFactor();
        $this->assertFalse($user->fresh()->hasEnabledTwoFactor());
    }
}
