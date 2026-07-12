<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // List of keys containing sensitive information that must be encrypted
    protected static array $sensitiveKeys = [
        'tripay_api_key',
        'tripay_private_key',
        'tripay_merchant_code'
    ];

    /**
     * Get setting value by key (with automated decryption for sensitive keys)
     */
    public static function get(string $key, $default = null): ?string
    {
        try {
            return \Illuminate\Support\Facades\Cache::rememberForever("setting.{$key}", function() use ($key, $default) {
                $setting = self::where('key', $key)->first();
                if ($setting && $setting->value !== null) {
                    if (in_array($key, self::$sensitiveKeys)) {
                        try {
                            return \Illuminate\Support\Facades\Crypt::decryptString($setting->value);
                        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                            // Fallback if the database value is still unencrypted
                            return $setting->value;
                        }
                    }
                    return $setting->value;
                }
                return $default;
            });
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set setting value by key (with automated encryption for sensitive keys)
     */
    public static function set(string $key, ?string $value): self
    {
        $finalValue = $value;
        if ($value !== null && in_array($key, self::$sensitiveKeys)) {
            $finalValue = \Illuminate\Support\Facades\Crypt::encryptString($value);
        }

        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $finalValue]
        );

        // Clear cache key when updated
        \Illuminate\Support\Facades\Cache::forget("setting.{$key}");

        return $setting;
    }
}
