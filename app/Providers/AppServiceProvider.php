<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $privateKey = storage_path('oauth-private.key');
        $publicKey = storage_path('oauth-public.key');

        $privLen = file_exists($privateKey) ? filesize($privateKey) : -1;
        $pubLen = file_exists($publicKey) ? filesize($publicKey) : -1;

        if ($privLen <= 0 || $pubLen <= 0) {
            $cnfPaths = glob('C:/wamp64/bin/php/php*/extras/ssl/openssl.cnf') ?: [];
            $cnf = !empty($cnfPaths) ? $cnfPaths[0] : null;

            $config = [
                'digest_alg' => 'sha256',
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ];

            if ($cnf && file_exists($cnf)) {
                $config['config'] = $cnf;
            }

            $keyPair = openssl_pkey_new($config);
            $err = '';
            while ($msg = openssl_error_string()) {
                $err .= $msg . '; ';
            }

            if (! $keyPair) {
                \Illuminate\Support\Facades\Log::error("openssl_pkey_new failed with cnf " . ($cnf ?? 'none') . ": {$err}");
            } else {
                openssl_pkey_export($keyPair, $privKey, null, $config);
                $pubKeyDetails = openssl_pkey_get_details($keyPair);
                $pubKey = $pubKeyDetails['key'] ?? null;

                if ($privKey && $pubKey) {
                    file_put_contents($privateKey, $privKey);
                    file_put_contents($publicKey, $pubKey);
                    \Illuminate\Support\Facades\Log::info("Generated oauth keys successfully");
                } else {
                    \Illuminate\Support\Facades\Log::error("Failed to export keys: privKey=" . strlen($privKey ?? '') . " pubKey=" . strlen($pubKey ?? ''));
                }
            }
        }

        // Ensure Personal Access Client exists
        try {
            $clientRepo = app(\Laravel\Passport\ClientRepository::class);
            try {
                $clientRepo->personalAccessClient('users');
            } catch (\Throwable $ex) {
                $clientRepo->createPersonalAccessGrantClient('HeyDiamond Personal Access Client', 'users');
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Personal access client check: " . $e->getMessage());
        }
    }
}
