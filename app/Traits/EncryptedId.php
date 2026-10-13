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
        return Crypt::encryptString((string) $id);
    }

    /**
     * Decrypt an ID
     */
    protected function decryptId(string $encryptedId): ?int
    {
        try {
            $decrypted = Crypt::decryptString($encryptedId);
            return (int) $decrypted;
        } catch (DecryptException $e) {
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
        return \Illuminate\Support\Facades\Crypt::encryptString((string) $id);
    }
}