<?php

namespace Omnipay\Redsys\Message;

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

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     *
     * @throws InvalidResponseException If order number is missing or signature does not match
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->merchantParameters = $this->decodeMerchantParameters($data['Ds_MerchantParameters']);
        if (!empty($this->merchantParameters['Ds_Order'])) {
            $order = $this->merchantParameters['Ds_Order'];
        } elseif (!empty($this->merchantParameters['DS_ORDER'])) {
            $order = $this->merchantParameters['DS_ORDER'];
        } else {
            throw new InvalidResponseException();
        }

        $this->returnSignature = $this->createReturnSignature(
            $data['Ds_MerchantParameters'],
            $order,
            base64_decode($this->request->getHmacKey())
        );

        if ($this->returnSignature != $data['Ds_Signature']) {
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
        return isset($this->merchantParameters['Ds_Response'])
            && is_numeric($this->merchantParameters['Ds_Response'])
            && 0 <= $this->merchantParameters['Ds_Response']
            && 100 > $this->merchantParameters['Ds_Response'];
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

    public function getTransactionReference()
    {
        return isset($this->merchantParameters['Ds_AuthorisationCode'])
            ? $this->merchantParameters['Ds_AuthorisationCode']
            : null;
    }

    public function getMessage()
    {
        return isset($this->merchantParameters['Ds_Response']) ? $this->merchantParameters['Ds_Response'] : null;
    }

    public function getCardType()
    {
        return isset($this->merchantParameters['Ds_Card_Type']) ? $this->merchantParameters['Ds_Card_Type'] : null;
    }
}
