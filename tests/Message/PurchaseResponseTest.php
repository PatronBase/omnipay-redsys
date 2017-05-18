<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    /** @var PurchaseResponse */
    private $response;

    /**
     * Set up for the tests in this class
     *
     * Uses the following data:
     *
     *      'Ds_Merchant_MerchantCode'       => '999008881',
     *      'Ds_Merchant_Terminal'           => '871',
     *      'Ds_Merchant_TransactionType'    => '0',
     *      'Ds_Merchant_Amount'             => '145',
     *      'Ds_Merchant_Currency'           => '978',
     *      'Ds_Merchant_Order'              => '0123abc',
     *      'Ds_Merchant_MerchantUrl'        => 'https://www.example.com/notify',
     *
     *      'Ds_Merchant_ProductDescription' => 'My sales items',
     *      'Ds_Merchant_Cardholder'         => 'J Smith',
     *      'Ds_Merchant_UrlOK'              => 'https://www.example.com/return',
     *      'Ds_Merchant_UrlKO'              => 'https://www.example.com/return',
     *      'Ds_Merchant_MerchantName'       => 'My Store',
     *      'Ds_Merchant_ConsumerLanguage'   => '002',
     *      'Ds_Merchant_MerchantData'       => 'Ref: 99zz',
     */
    public function setUp()
    {
        $this->response = new PurchaseResponse($this->getMockRequest(), array(
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
            'Ds_MerchantParameters' => 'eyJEc19NZXJjaGFudF9NZXJjaGFudENvZGUiOiI5OTkwMDg4ODEiLCJEc19NZXJjaGFudF9UZXJtaW5'
                .'hbCI6Ijg3MSIsIkRzX01lcmNoYW50X1RyYW5zYWN0aW9uVHlwZSI6IjAiLCJEc19NZXJjaGFudF9BbW91bnQiOiIxNDUiLCJEc19N'
                .'ZXJjaGFudF9DdXJyZW5jeSI6Ijk3OCIsIkRzX01lcmNoYW50X09yZGVyIjoiMDEyM2FiYyIsIkRzX01lcmNoYW50X01lcmNoYW50V'
                .'XJsIjoiaHR0cHM6XC9cL3d3dy5leGFtcGxlLmNvbVwvbm90aWZ5IiwiRHNfTWVyY2hhbnRfUHJvZHVjdERlc2NyaXB0aW9uIjoiTX'
                .'kgc2FsZXMgaXRlbXMiLCJEc19NZXJjaGFudF9DYXJkaG9sZGVyIjoiSiBTbWl0aCIsIkRzX01lcmNoYW50X1VybE9LIjoiaHR0cHM'
                .'6XC9cL3d3dy5leGFtcGxlLmNvbVwvcmV0dXJuIiwiRHNfTWVyY2hhbnRfVXJsS08iOiJodHRwczpcL1wvd3d3LmV4YW1wbGUuY29t'
                .'XC9yZXR1cm4iLCJEc19NZXJjaGFudF9NZXJjaGFudE5hbWUiOiJNeSBTdG9yZSIsIkRzX01lcmNoYW50X0NvbnN1bWVyTGFuZ3VhZ'
                .'2UiOiIwMDIiLCJEc19NZXJjaGFudF9NZXJjaGFudERhdGEiOiJSZWY6IDk5enoifQ==',
            'Ds_Signature' => 'dEYvw2ti+iUS9+sc1U8klNdLpoFPO08hRRzd9LLmLWs=',
        ));
    }

    public function testPurchaseSuccess()
    {
        $this->getMockRequest()->shouldReceive('getEndpoint')->once()
            ->andReturn('https://sis-t.redsys.es:25443/sis/realizarPago');

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
}
