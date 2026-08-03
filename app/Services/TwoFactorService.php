<?php

namespace App\Services;

use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorService
{
    public function setup(): array
    {
        $user = auth()->user();

        if ($user->hasTwoFactorEnabled()) {
            return [
                'enabled' => true,
                'qr_code' => null,
                'secret' => null,
            ];
        }

        $google2fa = new Google2FA;
        $secret = $google2fa->generateSecretKey();
        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return [
            'enabled' => false,
            'qr_code' => $qrCode,
            'secret' => $secret,
        ];
    }

    public function enable(string $secret, string $code): array
    {
        $user = auth()->user();
        $google2fa = new Google2FA;

        if (! $google2fa->verifyKey($secret, $code)) {
            throw new \InvalidArgumentException('Kode tidak valid. Silakan coba lagi.');
        }

        $recoveryCodes = collect(range(1, 8))->map(fn () => strtoupper(\Str::random(10)))->toArray();

        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
        ]);

        return ['success' => true, 'recovery_codes' => $recoveryCodes];
    }

    public function disable(string $code): array
    {
        $user = auth()->user();

        if (! $user->hasTwoFactorEnabled()) {
            throw new \InvalidArgumentException('2FA belum diaktifkan.');
        }

        $google2fa = new Google2FA;
        if (! $google2fa->verifyKey(decrypt($user->two_factor_secret), $code)) {
            throw new \InvalidArgumentException('Kode tidak valid.');
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return ['success' => true];
    }
}
