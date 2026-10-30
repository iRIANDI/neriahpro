<?php

namespace App\Services;

use Illuminate\Support\Str;

class TwoFactorService
{
    protected string $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Menghasilkan Base32 Secret Key (16 karakter standar Google Authenticator).
     */
    public function generateSecretKey(int $length = 16): string
    {
        $secret = '';
        $max = strlen($this->base32Chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $secret .= $this->base32Chars[random_int(0, $max)];
        }

        return $secret;
    }

    /**
     * Menghasilkan URI otpauth:// standar untuk QR Code.
     */
    public function getOtpAuthUri(string $company, string $holder, string $secret): string
    {
        $encodedCompany = rawurlencode($company);
        $encodedHolder = rawurlencode($holder);

        return "otpauth://totp/{$encodedCompany}:{$encodedHolder}?secret={$secret}&issuer={$encodedCompany}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Memverifikasi kode TOTP 6-digit terhadap secret key dengan toleransi waktu ±30 detik.
     */
    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTimeSlice = (int) floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $calculatedCode = $this->calculateCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Menghitung kode 6 digit untuk time slice tertentu (RFC 6238).
     */
    public function calculateCode(string $secret, int $timeSlice): string
    {
        $secretBinary = $this->base32Decode($secret);
        if ($secretBinary === false) {
            return '';
        }

        // Pack time slice into 8-byte big-endian binary string
        $timeBytes = pack('N*', 0) . pack('N*', $timeSlice);

        // HMAC-SHA1
        $hash = hash_hmac('sha1', $timeBytes, $secretBinary, true);

        // Dynamic truncation
        $offset = ord(substr($hash, -1)) & 0x0F;
        $hashPart = substr($hash, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        $modulo = $value % 1000000;

        return str_pad((string) $modulo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decode string Base32 menjadi binary.
     */
    protected function base32Decode(string $b32): string|false
    {
        $b32 = strtoupper(trim($b32));
        $b32 = rtrim($b32, '=');
        $l = strlen($b32);
        $n = 0;
        $j = 0;
        $binary = '';

        for ($i = 0; $i < $l; $i++) {
            $c = $b32[$i];
            $v = strpos($this->base32Chars, $c);
            if ($v === false) {
                return false;
            }

            $n = ($n << 5) | $v;
            $j += 5;

            if ($j >= 8) {
                $j -= 8;
                $binary .= chr(($n >> $j) & 0xFF);
            }
        }

        return $binary;
    }

    /**
     * Menghasilkan 8 kode pemulihan darurat (Recovery Codes) unik.
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $part1 = strtoupper(Str::random(5));
            $part2 = strtoupper(Str::random(5));
            $codes[] = "{$part1}-{$part2}";
        }
        return $codes;
    }

    /**
     * Menghasilkan QR Code Base64 Data URI secara aman dan offline tanpa API eksternal.
     */
    public function getQrCodeUrl(string $otpAuthUri): string
    {
        if (class_exists(\chillerlan\QRCode\QRCode::class)) {
            $options = new \chillerlan\QRCode\QROptions([
                'version'     => \chillerlan\QRCode\QRCode::VERSION_AUTO,
                'eccLevel'    => \chillerlan\QRCode\QRCode::ECC_M,
                'outputType'  => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'scale'       => 6,
                'imageBase64' => true,
            ]);

            return (new \chillerlan\QRCode\QRCode($options))->render($otpAuthUri);
        }

        // SVG fallback if chillerlan is absent
        return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><text x="10" y="100" font-family="sans-serif" font-size="12">Scan Authenticator</text></svg>';
    }
}

