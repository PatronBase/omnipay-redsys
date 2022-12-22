<?php

namespace Omnipay\Redsys;

use Omnipay\Common\AbstractGateway;
use Omnipay\Redsys\Message\CompletePurchaseRequest;
use Omnipay\Redsys\Message\PurchaseRequest;

/**
 * Redsys Redirect Gateway
 *
 * @link http://www.redsys.es/
 */
class RedirectGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Redsys Redirect';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId'      => '',
            'merchantName'    => '',
            'terminalId'      => '',
            'hmacKey'         => '',
            'protocolVersion' => '2.1.0', // default in case not set in the gateway config
            'testMode'        => false,
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantName()
    {
        return $this->getParameter('merchantName');
    }

    public function setMerchantName($value)
    {
        return $this->setParameter('merchantName', $value);
    }

    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    public function getHmacKey()
    {
        return $this->getParameter('hmacKey');
    }

    public function setHmacKey($value)
    {
        return $this->setParameter('hmacKey', $value);
    }

    public function getProtocolVersion()
    {
        return $this->getParameter('protocolVersion');
    }

    public function setProtocolVersion($value)
    {
        return $this->setParameter('protocolVersion', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Redsys\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Redsys\Message\CompletePurchaseRequest', $parameters);
    }
}
