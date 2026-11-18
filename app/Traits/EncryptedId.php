<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait EncryptedId
{
    /**
     * Encrypt an ID
     */
    protected function encryptId(int|string $id): string
    {
        return self::encryptIdStatic($id);
    }

    /**
     * Decrypt an ID
     */
    protected function decryptId(string $encryptedId): ?int
    {
        try {
            // Tokens are URL-safe base64 (see encryptIdStatic).
            $payload = strtr($encryptedId, '-_', '+/');
            $remainder = strlen($payload) % 4;

            if ($remainder !== 0) {
                $payload .= str_repeat('=', 4 - $remainder);
            }

            $decrypted = Crypt::decryptString($payload);
            return (int) $decrypted;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get the model instance by encrypted ID
     */
    protected function findByEncryptedId(string $modelClass, string $encryptedId, string $key = 'id')
    {
        $id = $this->decryptId($encryptedId);
        
        if ($id === null) {
            return null;
        }
        
        return $modelClass::where($key, $id)->first();
    }

    /**
     * Add encrypted ID to a model instance or collection
     */
    protected function addEncryptedId($model, string $key = 'id'): array
    {
        if ($model instanceof \Illuminate\Database\Eloquent\Collection) {
            return $model->map(function ($item) use ($key) {
                return $this->addEncryptedIdToModel($item, $key);
            })->toArray();
        }
        
        return $this->addEncryptedIdToModel($model, $key);
    }

    /**
     * Add encrypted ID to a single model
     */
    protected function addEncryptedIdToModel($model, string $key = 'id'): array
    {
        $data = $model->toArray();
        $data['encrypted_id'] = $this->encryptId($model->{$key});
        return $data;
    }

    /**
     * Static method to encrypt an ID (for use in resources)
     */
    public static function encryptIdStatic(int|string $id): string
    {
        $encrypted = \Illuminate\Support\Facades\Crypt::encryptString((string) $id);

        // Make the token safe to place directly in a URL path segment
        // (standard base64 uses "+", "/" and "=").
        return rtrim(strtr($encrypted, '+/', '-_'), '=');
    }
}