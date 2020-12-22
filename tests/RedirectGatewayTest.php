<?php

namespace Omnipay\Redsys;

use Omnipay\Tests\GatewayTestCase;

class RedirectGatewayTest extends GatewayTestCase
{
    /** @var array */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new RedirectGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'amount' => '1.45',
            'currency' => 'EUR',
            'merchantId' => '999008881',
            'merchantName' => 'My Store',
            'terminalId' => '871',
            'notifyUrl' => 'https://www.example.com/notify',
            'returnUrl' => 'https://www.example.com/return',
            'hmacKey' => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
            'transactionId' => '123abc',
            'testMode' => true,
        );
    }

    public function testPurchase()
    {
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertEquals('https://sis-t.redsys.es:25443/sis/realizarPago', $response->getRedirectUrl());
    }

    public function testCompletePurchaseSuccess()
    {
        $this->getHttpRequest()->request->replace(
            array(
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => 'eyJEc19TaWduYXR1cmVWZXJzaW9uIjoiSE1BQ19TSEEyNTZfVjEiLCJEc19EYXRlIjoiMTBcLzE'
                    .'xXC8yMDE1IiwiRHNfSG91ciI6IjEyOjAwIiwiRHNfU2VjdXJlUGF5bWVudCI6IjEiLCJEc19BbW91bnQiOiIxNDUiLCJEc19D'
                    .'dXJyZW5jeSI6Ijk3OCIsIkRzX09yZGVyIjoiMDEyM2FiYyIsIkRzX01lcmNoYW50Q29kZSI6Ijk5OTAwODg4MSIsIkRzX1Rlc'
                    .'m1pbmFsIjoiODcxIiwiRHNfUmVzcG9uc2UiOiIwMDAwIiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RG'
                    .'F0YSI6IlJlZjogOTl6eiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiOTk5OTk5IiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjI'
                    .'iLCJEc19DYXJkX0NvdW50cnkiOiI3MjQifQ==',
                'Ds_Signature' => 'wq466V5gAoRNWf_UyJfdS9VuNKElkHCfMQrTA0Oy4QE=',
            )
        );

        $response = $this->gateway->completePurchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('999999', $response->getTransactionReference());
        $this->assertSame(0, (int) $response->getMessage());
    }

    public function testCompletePurchaseFailure()
    {
        $this->getHttpRequest()->request->replace(
            array(
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => 'eyJEc19TaWduYXR1cmVWZXJzaW9uIjoiSE1BQ19TSEEyNTZfVjEiLCJEc19EYXRlIjoiMTBcLzE'
                    .'xXC8yMDE1IiwiRHNfSG91ciI6IjEyOjAwIiwiRHNfU2VjdXJlUGF5bWVudCI6IjAiLCJEc19BbW91bnQiOiIxNDUiLCJEc19D'
                    .'dXJyZW5jeSI6Ijk3OCIsIkRzX09yZGVyIjoiMDEyM2FiYyIsIkRzX01lcmNoYW50Q29kZSI6Ijk5OTAwODg4MSIsIkRzX1Rlc'
                    .'m1pbmFsIjoiODcxIiwiRHNfUmVzcG9uc2UiOiIwMTgwIiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RG'
                    .'F0YSI6IlJlZjogOTl6eiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiKysrKysrIiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjI'
                    .'iLCJEc19DYXJkX0NvdW50cnkiOiIwIn0=',
                'Ds_Signature' => '4cB7506qDYAqG8022GHWT2LwSeGvF5Q1cn7NNAKTrRY=',
            )
        );

        $response = $this->gateway->completePurchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(180, (int) $response->getMessage());
    }

    public function testCompletePurchaseError()
    {
        $this->getHttpRequest()->request->replace(
            array(
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => 'eyJEc19TaWduYXR1cmVWZXJzaW9uIjoiSE1BQ19TSEEyNTZfVjEiLCJEc19EYXRlIjoiMTBcLzE'
                    .'xXC8yMDE1IiwiRHNfSG91ciI6IjEyOjAwIiwiRHNfU2VjdXJlUGF5bWVudCI6IjAiLCJEc19BbW91bnQiOiIxNDUiLCJEc19D'
                    .'dXJyZW5jeSI6Ijk3OCIsIkRzX09yZGVyIjoiMDEyM2FiYyIsIkRzX01lcmNoYW50Q29kZSI6Ijk5OTAwODg4MSIsIkRzX1Rlc'
                    .'m1pbmFsIjoiODcxIiwiRHNfUmVzcG9uc2UiOiIwOTA5IiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RG'
                    .'F0YSI6IlJlZjogOTl6eiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiKysrKysrIiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjI'
                    .'iLCJEc19DYXJkX0NvdW50cnkiOiIwIn0=',
                'Ds_Signature' => 'YqXiWtntfc8bME-qgcsYsHggUApSBqICtXLTjQ7sFPc=',
            )
        );

        $response = $this->gateway->completePurchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(909, (int) $response->getMessage());
    }
}
