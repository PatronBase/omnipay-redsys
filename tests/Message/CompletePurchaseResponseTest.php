<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    /** @var CompletePurchaseResponse */
    private $response;

    public function testCompletePurchaseSuccess()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
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

        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame('999999', $this->response->getTransactionReference());
        $this->assertSame(0, (int) $this->response->getMessage());

        $checks = array(
            'Ds_SignatureVersion'  => 'HMAC_SHA256_V1',
            'Ds_Date'              => '10/11/2015',
            'Ds_Hour'              => '12:00',
            'Ds_SecurePayment'     => '1',
            'Ds_Amount'            => '145',
            'Ds_Currency'          => '978', // Euros
            'Ds_Order'             => '0123abc',
            'Ds_MerchantCode'      => '999008881',
            'Ds_Terminal'          => '871',
            'Ds_Response'          => '0000',
            'Ds_TransactionType'   => '0',
            'Ds_MerchantData'      => 'Ref: 99zz',
            'Ds_AuthorisationCode' => '999999',
            'Ds_ConsumerLanguage'  => '2',   // English
            'Ds_Card_Country'      => '724', // Spain
        );
        $this->runChecks($checks);
    }

    public function testCompletePurchaseFailure()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
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

        $this->assertFalse($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame(180, (int) $this->response->getMessage());

        $checks = array(
            'Ds_SignatureVersion'  => 'HMAC_SHA256_V1',
            'Ds_Date'              => '10/11/2015',
            'Ds_Hour'              => '12:00',
            'Ds_SecurePayment'     => '0',
            'Ds_Amount'            => '145',
            'Ds_Currency'          => '978', // Euros
            'Ds_Order'             => '0123abc',
            'Ds_MerchantCode'      => '999008881',
            'Ds_Terminal'          => '871',
            'Ds_Response'          => '0180',
            'Ds_TransactionType'   => '0',
            'Ds_MerchantData'      => 'Ref: 99zz',
            'Ds_AuthorisationCode' => '++++++',
            'Ds_ConsumerLanguage'  => '2', // English
            'Ds_Card_Country'      => '0',
        );
        $this->runChecks($checks);
    }

    public function testCompletePurchaseError()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
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

        $this->assertFalse($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame(909, (int) $this->response->getMessage());

        $checks = array(
            'Ds_SignatureVersion'  => 'HMAC_SHA256_V1',
            'Ds_Date'              => '10/11/2015',
            'Ds_Hour'              => '12:00',
            'Ds_SecurePayment'     => '0',
            'Ds_Amount'            => '145',
            'Ds_Currency'          => '978', // Euros
            'Ds_Order'             => '0123abc',
            'Ds_MerchantCode'      => '999008881',
            'Ds_Terminal'          => '871',
            'Ds_Response'          => '0909',
            'Ds_TransactionType'   => '0',
            'Ds_MerchantData'      => 'Ref: 99zz',
            'Ds_AuthorisationCode' => '++++++',
            'Ds_ConsumerLanguage'  => '2', // English
            'Ds_Card_Country'      => '0',
        );
        $this->runChecks($checks);
    }

    /**
     * @expectedException         Omnipay\Common\Exception\InvalidResponseException
     * @expectedExceptionMessage  Invalid response from payment gateway
     */
    public function testCompletePurchaseInvalidNoOrder()
    {
        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => '',
                'Ds_Signature' => '',
            )
        );
    }

    /**
     * @expectedException         Omnipay\Common\Exception\InvalidResponseException
     * @expectedExceptionMessage  Invalid response from payment gateway (signature mismatch)
     */
    public function testCompletePurchaseInvalidSignature()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => 'eyJEc19TaWduYXR1cmVWZXJzaW9uIjoiSE1BQ19TSEEyNTZfVjEiLCJEc19EYXRlIjoiMTBcLzE'
                    .'xXC8yMDE1IiwiRHNfSG91ciI6IjEyOjAwIiwiRHNfU2VjdXJlUGF5bWVudCI6IjAiLCJEc19BbW91bnQiOiIxNDUiLCJEc19D'
                    .'dXJyZW5jeSI6Ijk3OCIsIkRzX09yZGVyIjoiMDEyM2FiYyIsIkRzX01lcmNoYW50Q29kZSI6Ijk5OTAwODg4MSIsIkRzX1Rlc'
                    .'m1pbmFsIjoiODcxIiwiRHNfUmVzcG9uc2UiOiIwMTgwIiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RG'
                    .'F0YSI6IlJlZjogOTl6eiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiKysrKysrIiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjI'
                    .'iLCJEc19DYXJkX0NvdW50cnkiOiIwIn0=',
                'Ds_Signature' => '',
            )
        );
    }

    private function runChecks($checks)
    {
        $data = $this->response->getData();
        foreach ($checks as $key => $expected) {
            $this->assertSame($expected, $data[$key]);
        }     
    }
}
