<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Exception\RuntimeException;

/**
 * Security
 *
 * This class provides common encoding, decoding and signing functions.
 * While all of this code could be called statically, it is left as a
 * regular class in order to faciliate unit testing. If alternate
 * encryption methods are provided later, the VERSION const can be
 * switched to a constructor option (and validated against a whitelist).
 */
class Security
{
    /** @var string */
    const VERSION = 'HMAC_SHA256_V1';

    /**
     * Encode merchant parameters
     *
     * @param array $data  The parameters to encode
     *
     * @return string Encoded data
     */
    public function encodeMerchantParameters($data)
    {
        return base64_encode(json_encode($data));
    }

    /**
     * Decode merchant parameters
     *
     * @param string $data  The encoded string of parameters
     *
     * @return array Decoded data
     */
    public function decodeMerchantParameters($data)
    {
        return (array)json_decode(base64_decode(strtr($data, '-_', '+/')));
    }

    /**
     * Encrypt message with given key and default IV
     *
     * @param string $message  The message to encrypt
     * @param string $key      The base64-encoded key used to encrypt the message
     *
     * @return string Encrypted message
     *
     * @throws RuntimeException
     */
    protected function encryptMessage($message, $key)
    {
        $key = base64_decode($key);
        $iv = implode(array_map('chr', array(0, 0, 0, 0, 0, 0, 0, 0)));

        if ($this->hasValidEncryptionMethod()) {
            // OpenSSL needs to manually pad $message length to be mod 8 = 0; OPENSSL_ZERO_PADDING option doens't work
            if (strlen($message) % 8) {
                $message = str_pad($message, strlen($message) + 8 - strlen($message) % 8, "\0");
            }
            $ciphertext = openssl_encrypt($message, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $iv);
        } else {
            throw new RuntimeException('No valid encryption extension installed');
        }

        return $ciphertext;
    }

    /**
     * Check if the system has a valid encryption method available
     *
     * @return bool
     */
    public function hasValidEncryptionMethod()
    {
        return extension_loaded('openssl') && function_exists('openssl_encrypt');
    }

    /**
     * Create signature hash used to verify messages
     *
     * @todo Add if-check on algorithm to match against signature version as new param?
     *
     * @param string $message  The message to encrypt
     * @param string $salt     Unique salt used to generate the ciphertext
     * @param string $key      The base64-encoded key used to encrypt the message
     *
     * @return string Generated signature
     */
    public function createSignature($message, $salt, $key)
    {
        $ciphertext = $this->encryptMessage($salt, $key);
        return base64_encode(hash_hmac('sha256', $message, $ciphertext, true));
    }

    /**
     * Create signature hash used to verify messages back for Redirect gateway
     *
     * @param string $message  The message to encrypt
     * @param string $salt     Unique salt used to generate the ciphertext
     * @param string $key      The base64-encoded key used to encrypt the message
     *
     * @return string Generated signature
     */
    public function createReturnSignature($message, $salt, $key)
    {
        return strtr($this->createSignature($message, $salt, $key), '+/', '-_');
    }
}
