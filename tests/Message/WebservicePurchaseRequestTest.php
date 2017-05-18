<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class WebservicePurchaseRequestTest extends TestCase
{
    /** @var WebservicePurchaseRequest */
    private $request;

    public function setUp()
    {
        $this->request = new WebservicePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'merchantId'       => '999008881',
                'terminalId'       => '871',
                'amount'           => '1.45',
                'currency'         => 'EUR',
                'transactionId'    => '123abc',
                'hmacKey'          => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
                'card'             => new CreditCard(array(
                    'number'      => '4548812049400004',
                    'expiryMonth' => '12',
                    'expiryYear'  => '2020',
                    'cvv'         => '285',
                )),

                // undocumented fields
                // 'description'      => 'My sales items',
                // 'cardholder'       => 'J Smith',
                'merchantName'     => 'My Store',
                'consumerLanguage' => 'en',
                'merchantData'     => 'Ref: 99zz',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('999008881', $data['DATOSENTRADA']['DS_MERCHANT_MERCHANTCODE']);
        $this->assertSame('871', $data['DATOSENTRADA']['DS_MERCHANT_TERMINAL']);
        $this->assertSame('A', $data['DATOSENTRADA']['DS_MERCHANT_TRANSACTIONTYPE']);
        $this->assertSame(145, $data['DATOSENTRADA']['DS_MERCHANT_AMOUNT']);
        $this->assertSame('978', $data['DATOSENTRADA']['DS_MERCHANT_CURRENCY']);
        $this->assertSame('0123abc', $data['DATOSENTRADA']['DS_MERCHANT_ORDER']);

        $this->assertSame('4548812049400004', $data['DATOSENTRADA']['DS_MERCHANT_PAN']);
        $this->assertSame('2012', $data['DATOSENTRADA']['DS_MERCHANT_EXPIRYDATE']);
        $this->assertSame('285', $data['DATOSENTRADA']['DS_MERCHANT_CVV2']);

        // $this->assertSame('My sales items', $data['DATOSENTRADA']['DS_MERCHANT_PRODUCTDESCRIPTION']);
        // $this->assertSame('J Smith', $data['DATOSENTRADA']['DS_MERCHANT_CARDHOLDER']);
        $this->assertSame('My Store', $data['DATOSENTRADA']['DS_MERCHANT_MERCHANTNAME']);
        $this->assertSame('002', $data['DATOSENTRADA']['DS_MERCHANT_CONSUMERLANGUAGE']);
        $this->assertSame('Ref: 99zz', $data['DATOSENTRADA']['DS_MERCHANT_MERCHANTDATA']);

        $this->assertSame('HMAC_SHA256_V1', $data['DS_SIGNATUREVERSION']);
        // signature will change if undocumented fields added
        $this->assertSame('1RPtKuPpDldIa88VBPugTqm5BWJxoUWT0503BM/U5l4=', $data['DS_SIGNATURE']);
    }

    public function testGetHmacKey()
    {
        $this->assertSame('Mk9m98IfEblmPfrpsawt7BmxObt98Jev', $this->request->getHmacKey());
    }

    public function testGetDataTestMode()
    {
        $this->request->setTestMode(true);
        $this->assertSame('https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada', $this->request->getEndpoint());
        $this->request->setTestMode(false);
        $this->assertSame('https://sis.redsys.es/sis/services/SerClsWSEntrada', $this->request->getEndpoint());
    }
}
