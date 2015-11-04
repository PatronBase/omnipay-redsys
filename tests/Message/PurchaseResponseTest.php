<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Tests\TestCase;
use Mockery as m;

/**
 * Obscure the global function with a mocked function if possible
 */
function function_exists($name)
{
    return empty(PurchaseResponseTest::$functions)
        ? \function_exists($name)
        : PurchaseResponseTest::$functions->function_exists($name);
}

class PurchaseResponseTest extends TestCase
{
    public static $functions;
    /** @var PurchaseResponse */
    private $response;

    public function setUp()
    {
        self::$functions = m::mock();

        $this->response = new PurchaseResponse($this->getMockRequest(), array(
            'Ds_Merchant_MerchantCode'       => '999008881',
            'Ds_Merchant_Terminal'           => '871',
            'Ds_Merchant_TransactionType'    => '0',
            'Ds_Merchant_Amount'             => '145',
            'Ds_Merchant_Currency'           => '978',
            'Ds_Merchant_Order'              => '0123abc',
            'Ds_Merchant_MerchantUrl'        => 'https://www.example.com/notify',

            'Ds_Merchant_ProductDescription' => 'My sales items',
            'Ds_Merchant_Cardholder'         => 'J Smith',
            'Ds_Merchant_UrlOK'              => 'https://www.example.com/return',
            'Ds_Merchant_UrlKO'              => 'https://www.example.com/return',
            'Ds_Merchant_MerchantName'       => 'My Store',
            'Ds_Merchant_ConsumerLanguage'   => '002',
            'Ds_Merchant_MerchantData'       => 'Ref: 99zz',
        ));
    }

    public function tearDown()
    {
        m::close();
    }

    public function testPurchaseSuccess()
    {
        self::$functions->shouldReceive('function_exists')->with('mcrypt_encrypt')->once()->andReturn(true);
        $this->getMockRequest()->shouldReceive('getEndpoint')->once()->andReturn('https://sis-t.redsys.es:25443/sis/realizarPago');
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->assertFalse($this->response->isSuccessful());
        $this->assertTrue($this->response->isRedirect());
        $this->assertSame('https://sis-t.redsys.es:25443/sis/realizarPago', $this->response->getRedirectUrl());
        $this->assertSame('POST', $this->response->getRedirectMethod());
        $this->assertSame(
            array(
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => 'eyJEc19NZXJjaGFudF9NZXJjaGFudENvZGUiOiI5OTkwMDg4ODEiLCJEc19NZXJjaGFudF9UZXJ'
                    .'taW5hbCI6Ijg3MSIsIkRzX01lcmNoYW50X1RyYW5zYWN0aW9uVHlwZSI6IjAiLCJEc19NZXJjaGFudF9BbW91bnQiOiIxNDUi'
                    .'LCJEc19NZXJjaGFudF9DdXJyZW5jeSI6Ijk3OCIsIkRzX01lcmNoYW50X09yZGVyIjoiMDEyM2FiYyIsIkRzX01lcmNoYW50X'
                    .'01lcmNoYW50VXJsIjoiaHR0cHM6XC9cL3d3dy5leGFtcGxlLmNvbVwvbm90aWZ5IiwiRHNfTWVyY2hhbnRfUHJvZHVjdERlc2'
                    .'NyaXB0aW9uIjoiTXkgc2FsZXMgaXRlbXMiLCJEc19NZXJjaGFudF9DYXJkaG9sZGVyIjoiSiBTbWl0aCIsIkRzX01lcmNoYW5'
                    .'0X1VybE9LIjoiaHR0cHM6XC9cL3d3dy5leGFtcGxlLmNvbVwvcmV0dXJuIiwiRHNfTWVyY2hhbnRfVXJsS08iOiJodHRwczpc'
                    .'L1wvd3d3LmV4YW1wbGUuY29tXC9yZXR1cm4iLCJEc19NZXJjaGFudF9NZXJjaGFudE5hbWUiOiJNeSBTdG9yZSIsIkRzX01lc'
                    .'mNoYW50X0NvbnN1bWVyTGFuZ3VhZ2UiOiIwMDIiLCJEc19NZXJjaGFudF9NZXJjaGFudERhdGEiOiJSZWY6IDk5enoifQ==',
                'Ds_Signature' => 'dEYvw2ti+iUS9+sc1U8klNdLpoFPO08hRRzd9LLmLWs=',
            ),
            $this->response->getRedirectData()
        );
        $this->assertNull($this->response->getTransactionReference());
        $this->assertNull($this->response->getMessage());
    }

    public function testMcryptExists()
    {
        $this->assertTrue(extension_loaded('mcrypt'));
        $this->assertTrue(\function_exists('mcrypt_encrypt'));
    }

    /**
     * @depends testMcryptExists
     */
    public function testEncryptMessageSuccess()
    {
        self::$functions->shouldReceive('function_exists')->with('mcrypt_encrypt')->once()->andReturn(true);
        $cipher = unpack('H*', $this->encryptMessage());
        $this->assertSame('771c1265741bc77139c811410899bb11', $cipher[1]);
    }

    /**
     * @expectedException         Omnipay\Common\Exception\RuntimeException
     * @expectedExceptionMessage  No valid encryption extension installed
     */
    public function testEncryptMessageException()
    {
        self::$functions->shouldReceive('function_exists')->with('mcrypt_encrypt')->once()->andReturn(false);
        $this->encryptMessage();
    }

    /**
     * Helper method to test protected AbstractResponse::encryptMessage() method
     */
    protected function encryptMessage()
    {
        $class = new \ReflectionClass($this->response);
        $method = $class->getMethod('encryptMessage');
        $method->setAccessible(true);

        return $method->invokeArgs($this->response, array('test message', base64_decode('Mk9m98IfEblmPfrpsawt7BmxObt98Jev')));
    }
}
