<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /** @var PurchaseRequest */
    private $request;

    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'merchantId'       => '999008881',
                'terminalId'       => '871',
                'amount'           => '1.45',
                'currency'         => 'EUR',
                'transactionId'    => '123abc',
                'hmacKey'          => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',

                'description'      => 'My sales items',
                'cardholder'       => 'J Smith',
                'notifyUrl'        => 'https://www.example.com/notify',
                'returnUrl'        => 'https://www.example.com/return',
                'merchantName'     => 'My Store',
                'consumerLanguage' => 'en',
                'merchantData'     => 'Ref: 99zz',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('999008881', $data['Ds_Merchant_MerchantCode']);
        $this->assertSame('871', $data['Ds_Merchant_Terminal']);
        $this->assertSame('0', $data['Ds_Merchant_TransactionType']);
        $this->assertSame(145, $data['Ds_Merchant_Amount']);
        $this->assertSame('978', $data['Ds_Merchant_Currency']);
        $this->assertSame('0123abc', $data['Ds_Merchant_Order']);
        $this->assertSame('https://www.example.com/notify', $data['Ds_Merchant_MerchantUrl']);

        $this->assertSame('My sales items', $data['Ds_Merchant_ProductDescription']);
        $this->assertSame('J Smith', $data['Ds_Merchant_Cardholder']);
        $this->assertSame('https://www.example.com/return', $data['Ds_Merchant_UrlOK']);
        $this->assertSame('https://www.example.com/return', $data['Ds_Merchant_UrlKO']);
        $this->assertSame('My Store', $data['Ds_Merchant_MerchantName']);
        $this->assertSame('002', $data['Ds_Merchant_ConsumerLanguage']);
        $this->assertSame('Ref: 99zz', $data['Ds_Merchant_MerchantData']);
    }

    public function testGetDataTestMode()
    {
        $this->request->setTestMode(true);
        $this->assertSame('https://sis-t.redsys.es:25443/sis/realizarPago', $this->request->getEndpoint());
        $this->request->setTestMode(false);
        $this->assertSame('https://sis.redsys.es/sis/realizarPago', $this->request->getEndpoint());
    }

    public function testGetHmacKey()
    {
        $this->assertSame('Mk9m98IfEblmPfrpsawt7BmxObt98Jev', $this->request->getHmacKey());
    }

    public function testSetConsumerLanguage()
    {
        $this->request->setConsumerLanguage(1);
        $this->assertSame('001', $this->request->getConsumerLanguage());
        $this->request->setConsumerLanguage('en');
        $this->assertSame('002', $this->request->getConsumerLanguage());
    }
}
