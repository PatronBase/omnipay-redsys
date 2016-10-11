<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Redsys Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /** @var array */
    protected $merchantParameters;
    /** @var string */
    protected $returnSignature;
    /** @var boolean */
    protected $usingUpcaseParameters = false;
    /** @var boolean */
    protected $usingUpcaseResponse = false;

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     *
     * @throws InvalidResponseException If merchant data or order number is missing, or signature does not match
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $security = new Security;

        if (!empty($data['Ds_MerchantParameters'])) {
            $this->merchantParameters = $security->decodeMerchantParameters($data['Ds_MerchantParameters']);
        } elseif (!empty($data['DS_MERCHANTPARAMETERS'])) {
            $this->merchantParameters = $security->decodeMerchantParameters($data['DS_MERCHANTPARAMETERS']);
            $this->usingUpcaseResponse = true;
        } else {
            throw new InvalidResponseException('Invalid response from payment gateway (no data)');
        }

        if (!empty($this->merchantParameters['Ds_Order'])) {
            $order = $this->merchantParameters['Ds_Order'];
        } elseif (!empty($this->merchantParameters['DS_ORDER'])) {
            $order = $this->merchantParameters['DS_ORDER'];
            $this->usingUpcaseParameters = true;
        } else {
            throw new InvalidResponseException();
        }

        $this->returnSignature = $security->createReturnSignature(
            $data[$this->usingUpcaseResponse ? 'DS_MERCHANTPARAMETERS' : 'Ds_MerchantParameters'],
            $order,
            $this->request->getHmacKey()
        );

        if ($this->returnSignature != $data[$this->usingUpcaseResponse ? 'DS_SIGNATURE' : 'Ds_Signature']) {
            throw new InvalidResponseException('Invalid response from payment gateway (signature mismatch)');
        }
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        $key = $this->usingUpcaseParameters ? 'DS_RESPONSE' : 'Ds_Response';
        return isset($this->merchantParameters[$key])
            && is_numeric($this->merchantParameters[$key])
            && 0 <= $this->merchantParameters[$key]
            && 100 > $this->merchantParameters[$key];
    }

    /**
     * Get the response data, included the decoded merchant parameters if available.
     *
     * @return mixed
     */
    public function getData()
    {
        $data = parent::getData();
        return is_array($data) && is_array($this->merchantParameters)
            ? array_merge($data, $this->merchantParameters)
            : $data;
    }

    /**
     * Helper method to get a specific merchant parameter if available.
     *
     * @param string $key The key to look up
     *
     * @return null|mixed
     */
    protected function getKey($key)
    {
        if ($this->usingUpcaseParameters) {
            $key = strtoupper($key);
        }
        return isset($this->merchantParameters[$key]) ? $this->merchantParameters[$key] : null;
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
     * Get the card type if available.
     *
     * @return null|string
     */
    public function getCardType()
    {
        return $this->getKey('Ds_Card_Type');
    }
}
