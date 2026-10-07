<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\CustomerOtpMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    const OTP_EXPIRY_MINUTES = 10;
    const MAX_VERIFY_ATTEMPTS = 5;
    const RATE_LIMIT_MINUTES = 10;
    const MAX_REQUESTS_PER_WINDOW = 3;

    /**
     * Request a 6-digit OTP code to be sent to customer email.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'Alamat e-mail wajib diisi.',
            'email.email' => 'Format alamat e-mail tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $clientIp = $request->ip() ?: '127.0.0.1';

        // 1. Rate limiting by IP and Email
        $throttleKey = 'otp_throttle_' . md5($email . '_' . $clientIp);
        $requestCount = (int) Cache::get($throttleKey, 0);

        if ($requestCount >= self::MAX_REQUESTS_PER_WINDOW) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak permintaan OTP. Demi keamanan, silakan tunggu 10 menit sebelum mencoba lagi.',
            ], 429);
        }

        // 2. Generate 6-digit cryptographically secure OTP
        $otpCode = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // 3. Cache the hashed OTP with metadata
        $cacheKey = 'customer_otp_' . md5($email);
        Cache::put($cacheKey, [
            'hash' => Hash::make($otpCode),
            'attempts' => 0,
            'created_at' => now()->timestamp,
            'ip' => $clientIp,
        ], now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        // Increment throttle counter
        Cache::put($throttleKey, $requestCount + 1, now()->addMinutes(self::RATE_LIMIT_MINUTES));

        // 4. Send Email Notification
        try {
            Mail::to($email)->send(new CustomerOtpMail(
                otp: $otpCode,
                email: $email,
                ipAddress: $clientIp,
                expiryMinutes: self::OTP_EXPIRY_MINUTES
            ));
        } catch (\Throwable $e) {
            \Log::error('Gagal mengirimkan email OTP: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirimkan kode OTP ke email. Pastikan koneksi mail server terkonfigurasi dengan benar.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6-digit berhasil dikirimkan ke e-mail ' . $email . '. Periksa kotak masuk atau spam.',
            'expires_in_minutes' => self::OTP_EXPIRY_MINUTES,
        ]);
    }

    /**
     * Verify the 6-digit OTP code and authenticate customer.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'otp' => 'required|string|size:6',
        ], [
            'email.required' => 'Alamat e-mail wajib diisi.',
            'otp.required' => 'Kode OTP 6-digit wajib diisi.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 digit angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $inputOtp = trim((string) $request->input('otp'));
        $cacheKey = 'customer_otp_' . md5($email);

        $record = Cache::get($cacheKey);

        if (!$record || empty($record['hash'])) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak ditemukan atau telah kedaluwarsa. Silakan minta kode baru.',
            ], 422);
        }

        // Check if exceeded max attempts
        if (($record['attempts'] ?? 0) >= self::MAX_VERIFY_ATTEMPTS) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan kode salah. Kode OTP telah dibatalkan demi keamanan. Silakan minta kode baru.',
            ], 429);
        }

        // Verify hash
        if (!Hash::check($inputOtp, $record['hash'])) {
            $record['attempts'] = ($record['attempts'] ?? 0) + 1;
            $remaining = self::MAX_VERIFY_ATTEMPTS - $record['attempts'];
            Cache::put($cacheKey, $record, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

            return response()->json([
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan salah. Sisa kesempatan: ' . max(0, $remaining) . ' kali.',
                'remaining_attempts' => max(0, $remaining),
            ], 422);
        }

        // Successfully verified -> Purge OTP from cache
        Cache::forget($cacheKey);

        // Find or create Customer user with ULID
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => explode('@', $email)[0],
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        // Authenticate into session
        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Autentikasi OTP berhasil! Selamat datang di Portal Neriah Pro.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_super_admin' => $user->isSuperAdmin(),
            ],
            'redirect_url' => session()->pull('url.intended', '/blueprint'),
        ]);
    }

    /**
     * Check customer login status.
     */
    public function status(Request $request): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'logged_in' => Auth::check(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_super_admin' => $user->isSuperAdmin(),
            ] : null,
        ]);
    }

    /**
     * Log out customer session.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Sesi Anda berhasil diakhiri.',
        ]);
    }
}
