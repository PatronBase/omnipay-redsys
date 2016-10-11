<?php

namespace Omnipay\Redsys;

use Omnipay\Common\AbstractGateway;
use Omnipay\Redsys\Message\CompletePurchaseRequest;
use Omnipay\Redsys\Message\PurchaseRequest;

/**
 * Redsys Webservice Gateway
 *
 * @link http://www.redsys.es/
 */
class WebserviceGateway extends RedirectGateway
{
    public function getName()
    {
        return 'Redsys Webservice';
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Redsys\Message\WebservicePurchaseRequest', $parameters);
    }
}
