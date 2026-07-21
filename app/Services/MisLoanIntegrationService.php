<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;

class MisLoanIntegrationService
{
    public function integrationUsername(): string
    {
        return trim((string) config('services.misloan.integration_username', 'superadmin'));
    }

    public function integrationUser(): ?User
    {
        $username = $this->integrationUsername();

        return User::query()
            ->where('username', $username)
            ->first();
    }

    public function activeToken(): ?PersonalAccessToken
    {
        $user = $this->integrationUser();
        if (! $user) {
            return null;
        }

        return PersonalAccessToken::query()
            ->where('tokenable_type', $user->getMorphClass())
            ->where('tokenable_id', $user->id)
            ->where('name', 'misloan-integration')
            ->latest('id')
            ->first();
    }

    /**
     * @return array{ok: bool, plain_token: string, username: string}
     */
    public function generateToken(): array
    {
        $user = $this->integrationUser();
        if (! $user) {
            throw new \RuntimeException(
                'Integration user "'.$this->integrationUsername().'" block list-এ পাওয়া যায়নি।'
            );
        }

        $user->tokens()->where('name', 'misloan-integration')->delete();

        $plainToken = $user->createToken('misloan-integration')->plainTextToken;

        return [
            'ok' => true,
            'plain_token' => $plainToken,
            'username' => (string) $user->username,
        ];
    }

    public function revokeToken(): void
    {
        $user = $this->integrationUser();
        $user?->tokens()->where('name', 'misloan-integration')->delete();
    }

    /**
     * @return array{ok: bool, message: string, status?: int}
     */
    public function testApiReachable(string $apiBaseUrl): array
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get(rtrim($apiBaseUrl, '/').'/auth/login');

            if ($response->status() === 405 || $response->status() === 422 || $response->successful()) {
                return ['ok' => true, 'message' => 'Block List API সার্ভারে সংযোগ সফল।'];
            }

            return [
                'ok' => false,
                'message' => 'API সার্ভার সাড়া দিচ্ছে না (HTTP '.$response->status().').',
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'API সংযোগ করা যায়নি: '.$e->getMessage()];
        }
    }
}
