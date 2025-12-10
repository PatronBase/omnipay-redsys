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
                'Ds_MerchantParameters' => 'eyJEc19EYXRlIjoiMTBcLzExXC8yMDE1IiwiRHNfSG91ciI6IjEyOjAwIiwiRHNfU2VjdXJlUGF'
                    .'5bWVudCI6IjEiLCJEc19BbW91bnQiOiIxNDUiLCJEc19DdXJyZW5jeSI6Ijk3OCIsIkRzX09yZGVyIjoiMDEyM2FiYyIsIkRz'
                    .'X01lcmNoYW50Q29kZSI6Ijk5OTAwODg4MSIsIkRzX1Rlcm1pbmFsIjoiODcxIiwiRHNfUmVzcG9uc2UiOiIwMDAwIiwiRHNfV'
                    .'HJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RGF0YSI6IlJlZjogOTl6eiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIj'
                    .'oiOTk5OTk5IiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjIiLCJEc19DYXJkX0NvdW50cnkiOiI3MjQiLCJEc19DYXJkX1R5cGU'
                    .'iOiJDIn0=',
                'Ds_Signature' => '5v_0NCL0OBXM2CsZUSNdGQKRvmc3itFvM_WgiKe-pKA=',
            )
        );

        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame('999999', $this->response->getTransactionReference());
        $this->assertSame(0, (int) $this->response->getMessage());
        $this->assertSame('C', $this->response->getCardType());
        $this->assertNull($this->response->getCardReference());

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
            'Ds_Card_Type'         => 'C',   // Credit
        );
        $this->runChecks($checks);
    }

    public function testCompletePurchaseSuccessUpperParameters()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'DS_SIGNATUREVERSION' => 'HMAC_SHA256_V1',
                'DS_MERCHANTPARAMETERS' => 'eyJEU19EQVRFIjoiMTBcLzExXC8yMDE1IiwiRFNfSE9VUiI6IjEyOjAwIiwiRFNfU0VDVVJFUEF'
                    .'ZTUVOVCI6IjEiLCJEU19BTU9VTlQiOiIxNDUiLCJEU19DVVJSRU5DWSI6Ijk3OCIsIkRTX09SREVSIjoiMDEyM2FiYyIsIkRT'
                    .'X01FUkNIQU5UQ09ERSI6Ijk5OTAwODg4MSIsIkRTX1RFUk1JTkFMIjoiODcxIiwiRFNfUkVTUE9OU0UiOiIwMDAwIiwiRFNfV'
                    .'FJBTlNBQ1RJT05UWVBFIjoiMCIsIkRTX01FUkNIQU5UREFUQSI6IlJlZjogOTl6eiIsIkRTX0FVVEhPUklTQVRJT05DT0RFIj'
                    .'oiOTk5OTk5IiwiRFNfQ09OU1VNRVJMQU5HVUFHRSI6IjIiLCJEU19DQVJEX0NPVU5UUlkiOiI3MjQiLCJEU19DQVJEX1RZUEU'
                    .'iOiJDIn0=',
                'DS_SIGNATURE' => 'skOah02ucd3CI_bVXJk0sRnaY_bg9Pq7OqvpCBC30Fs=',
            )
        );

        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame('999999', $this->response->getTransactionReference());
        $this->assertSame(0, (int) $this->response->getMessage());
        $this->assertSame('C', $this->response->getCardType());
        $this->assertNull($this->response->getCardReference());

        $checks = array(
            'DS_SIGNATUREVERSION'  => 'HMAC_SHA256_V1',
            'DS_DATE'              => '10/11/2015',
            'DS_HOUR'              => '12:00',
            'DS_SECUREPAYMENT'     => '1',
            'DS_AMOUNT'            => '145',
            'DS_CURRENCY'          => '978', // Euros
            'DS_ORDER'             => '0123abc',
            'DS_MERCHANTCODE'      => '999008881',
            'DS_TERMINAL'          => '871',
            'DS_RESPONSE'          => '0000',
            'DS_TRANSACTIONTYPE'   => '0',
            'DS_MERCHANTDATA'      => 'Ref: 99zz',
            'DS_AUTHORISATIONCODE' => '999999',
            'DS_CONSUMERLANGUAGE'  => '2',   // English
            'DS_CARD_COUNTRY'      => '724', // Spain
            'DS_CARD_TYPE'         => 'C',   // Credit
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
        $this->assertNull($this->response->getCardType());
        $this->assertNull($this->response->getCardReference());

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
        $this->assertNull($this->response->getCardType());
        $this->assertNull($this->response->getCardReference());

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

    public function testCompletePurchaseReturnsCardReference()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            [
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => 'eyJEc19EYXRlIjoiMTBcLzExXC8yMDE1IiwiRHNfSG91ciI6IjEyOjAwIiwiRHNfU2VjdXJlUG'
                    .'F5bWVudCI6IjEiLCJEc19BbW91bnQiOiIxNDUiLCJEc19DdXJyZW5jeSI6Ijk3OCIsIkRzX09yZGVyIjoiMDEyM2FiYyIsIk'
                    .'RzX01lcmNoYW50Q29kZSI6Ijk5OTAwODg4MSIsIkRzX1Rlcm1pbmFsIjoiODcxIiwiRHNfUmVzcG9uc2UiOiIwMDAwIiwiRH'
                    .'NfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RGF0YSI6IlJlZjogOTl6eiIsIkRzX0F1dGhvcmlzYXRpb25Db2'
                    .'RlIjoiOTk5OTk5IiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjIiLCJEc19DYXJkX0NvdW50cnkiOiI3MjQiLCJEc19DYXJkX1'
                    .'R5cGUiOiJDIiwiRHNfTWVyY2hhbnRfSWRlbnRpZmllciI6IjEyMzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MDEyMzQ1Nj'
                    .'c4OTAifQ==',
                'Ds_Signature' => 'D_t6g3K47mE_DtF8ZHjmZBFw54E_lFxNVsZJ0NbEX2o=',
            ]
        );

        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame('1234567890123456789012345678901234567890', $this->response->getCardReference());
    }

    public function testCompletePurchaseInvalidNoParameters()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway (no data)');
        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => '',
                'Ds_Signature' => '',
            )
        );
    }

    public function testCompletePurchaseInvalidNoOrder()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway');
        $this->response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
                'Ds_MerchantParameters' => 'eyJEc19EYXRlIjoiMTBcLzExXC8yMDE1IiwiRHNfSG91ciI6IjEyOjAwIiwiRHNfU2VjdXJlUGF'
                    .'5bWVudCI6IjEiLCJEc19BbW91bnQiOiIxNDUiLCJEc19DdXJyZW5jeSI6Ijk3OCIsIkRzX01lcmNoYW50Q29kZSI6Ijk5OTAw'
                    .'ODg4MSIsIkRzX1Rlcm1pbmFsIjoiODcxIiwiRHNfUmVzcG9uc2UiOiIwMDAwIiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsI'
                    .'kRzX01lcmNoYW50RGF0YSI6IlJlZjogOTl6eiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiOTk5OTk5IiwiRHNfQ29uc3VtZX'
                    .'JMYW5ndWFnZSI6IjIiLCJEc19DYXJkX0NvdW50cnkiOiI3MjQiLCJEc19DYXJkX1R5cGUiOiJDIn0=',
                'Ds_Signature' => '4cB7506qDYAqG8022GHWT2LwSeGvF5Q1cn7NNAKTrRY=',
            )
        );
    }

    public function testCompletePurchaseInvalidSignature()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway (signature mismatch)');

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
