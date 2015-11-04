<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Redsys Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $version = 'HMAC_SHA256_V1';

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->getRequest()->getEndpoint();
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        $redirect_data = array();
        $redirect_data['Ds_SignatureVersion'] = $this->version;
        $redirect_data['Ds_MerchantParameters'] = $this->encodeMerchantParameters($this->data);
        $redirect_data['Ds_Signature'] = $this->createSignature(
            $redirect_data['Ds_MerchantParameters'],
            $this->data['Ds_Merchant_Order'],
            base64_decode($this->request->getHmacKey())
        );

        return $redirect_data;
    }
}
