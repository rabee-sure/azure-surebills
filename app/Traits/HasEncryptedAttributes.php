<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Trait HasEncryptedAttributes
 * 
 * Automatically encrypts and decrypts specified model attributes.
 * 
 * Usage:
 * 
 * class User extends Model {
 *     use HasEncryptedAttributes;
 * 
 *     protected $encrypted = [
 *         'iban_number',
 *         'credit_card',
 *         'ssn',
 *         'phone',
 *     ];
 * }
 * 
 * @package App\Traits
 */
trait HasEncryptedAttributes
{
    /**
     * Encrypt attributes before storing.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->shouldEncryptAttribute($key) && !empty($value) && !$this->isEncrypted($value)) {
            $value = encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Decrypt attributes when accessing.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($this->shouldEncryptAttribute($key) && !empty($value)) {
            return $this->decryptAttribute($value, $key);
        }

        return $value;
    }

    /**
     * Decrypt attribute value.
     *
     * @param  string  $value
     * @param  string  $key
     * @return string
     */
    protected function decryptAttribute($value, $key)
    {
        // Check if value looks encrypted (starts with "eyJ" - base64 encoded JSON)
        if ($this->isEncrypted($value)) {
            try {
                return decrypt($value);
            } catch (\Exception $e) {
                // Decryption failed - log error
                Log::error(sprintf(
                    "Failed to decrypt attribute '%s' for %s ID %s: %s",
                    $key,
                    class_basename($this),
                    $this->getKey() ?? 'unknown',
                    $e->getMessage()
                ));
                return $value;
            }
        }

        // Not encrypted - return as is (for backward compatibility with existing data)
        return $value;
    }

    /**
     * Check if a value is already encrypted.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function isEncrypted($value)
    {
        if (!is_string($value) || empty($value)) {
            return false;
        }

        // Laravel's encrypted values are base64 encoded JSON
        // They typically start with "eyJ" (base64 of {"iv":)
        return strpos($value, 'eyJ') === 0;
    }

    /**
     * Determine if an attribute should be encrypted.
     * 
     * Checks the $encrypted property on the model.
     * 
     * @param  string  $key
     * @return bool
     */
    protected function shouldEncryptAttribute($key)
    {
        // Support both $encrypted and $encryptedAttributes property names
        $encryptedAttributes = $this->encrypted ?? $this->encryptedAttributes ?? [];

        return in_array($key, $encryptedAttributes);
    }

    /**
     * Get the list of encrypted attributes.
     *
     * @return array
     */
    public function getEncryptedAttributes()
    {
        return $this->encrypted ?? $this->encryptedAttributes ?? [];
    }
}

