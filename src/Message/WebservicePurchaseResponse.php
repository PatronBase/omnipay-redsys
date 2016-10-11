<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Redsys Purchase Response
 */
class WebservicePurchaseResponse extends AbstractResponse
{
    /** @var string */
    protected $returnSignature;
    /** @var boolean */
    protected $usingUpcaseResponse = false;

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     *
     * @throws InvalidResponseException If resopnse format is incorrect, data is missing, or signature does not match
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $security = new Security;

        if (!isset($data['CODIGO'])) {
            throw new InvalidResponseException('Invalid response from payment gateway (no data)');
        }
        if (!isset($data['OPERACION'])) {
            if ($data['CODIGO'] == '0') {
                throw new InvalidResponseException('Invalid response from payment gateway (no data)');
            } else {
                throw new InvalidResponseException('Invalid response from payment gateway ('.$data['CODIGO'].')');
            }
        }

        if (isset($data['OPERACION']['DS_ORDER'])) {
            $this->usingUpcaseResponse = true;
        }

        $order = $this->GetKey('Ds_Order');
        if ($order === null) {
            throw new InvalidResponseException();
        }

        $signature_keys = array(
            'Ds_Amount',
            'Ds_Order',
            'Ds_MerchantCode',
            'Ds_Currency',
            'Ds_Response',
            'Ds_TransactionType',
            'Ds_SecurePayment',
        );
        $signature_data = '';
        foreach ($signature_keys as $key) {
            $value = $this->getKey($key);
            if ($value === null) {
                throw new InvalidResponseException('Invalid response from payment gateway (missing data)');
            }
            $signature_data .= $value;
        }

        $this->returnSignature = $security->createSignature(
            $signature_data,
            $order,
            $this->request->getHmacKey()
        );

        if ($this->returnSignature != $this->GetKey('Ds_Signature')) {
            throw new InvalidResponseException('Invalid response from payment gateway (signature mismatch)');
        }
    }

    public function isSuccessful()
    {
        $response_code = $this->getKey('Ds_Response');

        // check for field existence as well as value
        return isset($this->data['CODIGO'])
            && $this->data['CODIGO'] == '0'
            && $response_code !== null
            && is_numeric($response_code)
            && 0 <= $response_code
            && 100 > $response_code;
    }

     /**
     * Helper method to get a specific response parameter if available.
     *
     * @param string $key The key to look up
     *
     * @return null|mixed
     */
    protected function getKey($key)
    {
        if ($this->usingUpcaseResponse) {
            $key = strtoupper($key);
        }
        return isset($this->data['OPERACION'][$key]) ? $this->data['OPERACION'][$key] : null;
    }

    /**
     * Get the authorisation code if available.
     *
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->getKey('Ds_AuthorisationCode');
    }

    /**
     * Get the merchant response message if available.
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->getKey('Ds_Response');
    }

    /**
     * Get the merchant data if available.
     *
     * @return null|string
     */
    public function getMerchantData()
    {
        return $this->getKey('Ds_MerchantData');
    }

    /**
     * Get the card country if available.
     *
     * @return null|string  ISO 3166-1 (3-digit numeric) format, if supplied
     */
    public function getCardCountry()
    {
        return $this->getKey('Ds_Card_Country');
    }
}
