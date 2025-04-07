<?php

namespace Omnipay\Redsys;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\GatewayTestCase;

class WebserviceGatewayTest extends GatewayTestCase
{
    /** @var array */
    protected $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new WebserviceGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'amount' => '1.45',
            'currency' => 'EUR',
            'merchantId' => '999008881',
            'merchantName' => 'My Store',
            'terminalId' => '871',
            'hmacKey' => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
            'transactionId' => '123abc',
            'testMode' => true,
        );
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('WebservicePurchaseSuccess.txt');

        $this->options['card'] = new CreditCard(array(
            'number'           => '4548812049400004',
            'expiryMonth'      => '12',
            'expiryYear'       => '2020',
            'cvv'              => '285',
        ));

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('999999', $response->getTransactionReference());
        $this->assertSame(0, (int) $response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('WebservicePurchaseFailure.txt');

        $this->options['card'] = new CreditCard(array(
            'number'           => '1111111111111117',
            'expiryMonth'      => '12',
            'expiryYear'       => '2020',
            'cvv'              => '285',
        ));

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(180, (int) $response->getMessage());
    }

    public function testPurchaseError()
    {
        $this->setMockHttpResponse('WebservicePurchaseError.txt');

        $this->options['card'] = new CreditCard(array(
            'number'           => '9999999999999999',
            'expiryMonth'      => '12',
            'expiryYear'       => '2020',
            'cvv'              => '285',
        ));

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(909, (int) $response->getMessage());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testPurchaseInvalid()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway (SIS0438)');
 
        $this->setMockHttpResponse('WebservicePurchaseInvalid.txt');

        $this->options['card'] = new CreditCard(array(
            'number'           => '9999999999999999',
            'expiryMonth'      => '12',
            'expiryYear'       => '2020',
            'cvv'              => '285',
        ));

        $response = $this->gateway->purchase($this->options)->send();
    }
}
