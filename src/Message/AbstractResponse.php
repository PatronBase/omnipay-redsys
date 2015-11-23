<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Message\AbstractResponse as BaseAbstractResponse;
use Omnipay\Common\Exception\RuntimeException;

/**
 * Abstract Response
 *
 * This abstract class extends the base Omnipay AbstractResponse in order
 * to provide some common encoding and decoding functions.
 */
abstract class AbstractResponse extends BaseAbstractResponse
{
    /**
     * Encode merchant parameters
     *
     * @param array $data  The parameters to encode
     *
     * @return string Encoded data
     */
    protected function encodeMerchantParameters($data)
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
    protected function decodeMerchantParameters($data)
    {
        return (array)json_decode(base64_decode(strtr($data, '-_', '+/')));
    }

    /**
     * Encrypt message with given key and default IV
     *
     * @todo function_exists() vs extension_loaded()?
     *
     * @param string $message  The message to encrypt
     * @param string $key      The key used to encrypt the message
     *
     * @return string Encrypted message
     *
     * @throws RuntimeException
     */
    protected function encryptMessage($message, $key)
    {
        $iv = implode(array_map('chr', array(0, 0, 0, 0, 0, 0, 0, 0)));

        if (function_exists('mcrypt_encrypt')) {
            $ciphertext = mcrypt_encrypt(MCRYPT_3DES, $key, $message, MCRYPT_MODE_CBC, $iv);
        } else {
            throw new RuntimeException('No valid encryption extension installed');
        }

        return $ciphertext;
    }

    /**
     * Create signature hash used to verify messages
     *
     * @todo Add if-check on algorithm to match against signature version as new param?
     *
     * @param string $message  The message to encrypt
     * @param string $salt     Unique salt used to generate the ciphertext
     * @param string $key      The key used to encrypt the message
     *
     * @return string Generated signature
     */
    protected function createSignature($message, $salt, $key)
    {
        $ciphertext = $this->encryptMessage($salt, $key);
        return base64_encode(hash_hmac('sha256', $message, $ciphertext, true));
    }

    protected function createReturnSignature($message, $salt, $key)
    {
        return strtr($this->createSignature($message, $salt, $key), '+/', '-_');
    }
}
