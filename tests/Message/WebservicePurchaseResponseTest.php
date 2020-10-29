<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Tests\TestCase;

class WebservicePurchaseResponseTest extends TestCase
{
    /** @var WebservicePurchaseResponse */
    private $response;

    public function testPurchaseSuccess()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => '0',
                'OPERACION' => array(
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => 'mQF1RU05OZAKwkn7XWDFayiJwWZAI6MxUqyyR50HkPQ=',
                    'Ds_MerchantCode' => '999008881',
                    'Ds_Terminal' => '871',
                    'Ds_Response' => '0000',
                    'Ds_AuthorisationCode' => '999999',
                    'Ds_TransactionType' => 'A',
                    'Ds_SecurePayment' => '0',
                    'Ds_Language' => '2',
                    // undocumented
                    'Ds_MerchantData' => 'Ref: 99zz',
                    'Ds_Card_Country' => '724',
                ),
            )
        );

        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame('999999', $this->response->getTransactionReference());
        $this->assertSame(0, (int) $this->response->getMessage());
    }

    public function testPurchaseSuccessUpperResponse()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => '0',
                'OPERACION' => array(
                    'DS_AMOUNT' => '145',
                    'DS_CURRENCY' => '978',
                    'DS_ORDER' => '0123abc',
                    'DS_SIGNATURE' => 'mQF1RU05OZAKwkn7XWDFayiJwWZAI6MxUqyyR50HkPQ=',
                    'DS_MERCHANTCODE' => '999008881',
                    'DS_TERMINAL' => '871',
                    'DS_RESPONSE' => '0000',
                    'DS_AUTHORISATIONCODE' => '999999',
                    'DS_TRANSACTIONTYPE' => 'A',
                    'DS_SECUREPAYMENT' => '0',
                    'DS_LANGUAGE' => '2',
                    // undocumented
                    'DS_MERCHANTDATA' => 'Ref: 99zz',
                    'DS_CARD_COUNTRY' => '724',
                ),
            )
        );

        $this->assertTrue($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame('999999', $this->response->getTransactionReference());
        $this->assertSame(0, (int) $this->response->getMessage());
        $this->assertSame('Ref: 99zz', $this->response->getMerchantData());
        $this->assertSame(724, (int) $this->response->getCardCountry());
    }

    public function testPurchaseFailure()
    {
        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => '0',
                'OPERACION' => array(
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => 'xyBfo3NLgmsDDXaUjTkBmM8vOD8X/jrNDaBAAN2qMyE=',
                    'Ds_MerchantCode' => '999008881',
                    'Ds_Terminal' => '871',
                    'Ds_Response' => '0180',
                    'Ds_AuthorisationCode' => '++++++',
                    'Ds_TransactionType' => 'A',
                    'Ds_SecurePayment' => '0',
                    'Ds_Language' => '2',
                    // undocumented
                    'Ds_MerchantData' => 'Ref: 99zz',
                    'Ds_Card_Country' => '0',
                ),
            )
        );
        $this->assertFalse($this->response->isSuccessful());
        $this->assertFalse($this->response->isRedirect());
        $this->assertSame(180, (int) $this->response->getMessage());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testPurchaseInvalidNoReturnCode()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway (no data)');
        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'OPERACION' => array(),
            )
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testPurchaseInvalidNoTransactionData()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway (no data)');
        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => '0',
            )
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testPurchaseIntegrationError()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway (SIS0042)');
        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => 'SIS0042',
            )
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCompletePurchaseInvalidNoOrder()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway');
        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => '0',
                'OPERACION' => array(
                    'Ds_Amount' => '145',
                ),
            )
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCompletePurchaseInvalidMissingData()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway (missing data)');
        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => '0',
                'OPERACION' => array(
                    'Ds_Amount' => '145',
                    'Ds_Order' => '0123abc',
                ),
            )
        );
    }


    /**
     * @doesNotPerformAssertions
     */
    public function testPurchaseBadSignature()
    {
        $this->expectException('Omnipay\Common\Exception\InvalidResponseException');
        $this->expectExceptionMessage('Invalid response from payment gateway (signature mismatch)');

        $this->getMockRequest()->shouldReceive('getHmacKey')->once()->andReturn('Mk9m98IfEblmPfrpsawt7BmxObt98Jev');

        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => '0',
                'OPERACION' => array(
                    'Ds_Amount' => '145',
                    'Ds_Currency' => '978',
                    'Ds_Order' => '0123abc',
                    'Ds_Signature' => '',
                    'Ds_MerchantCode' => '999008881',
                    'Ds_Terminal' => '871',
                    'Ds_Response' => '0000',
                    'Ds_AuthorisationCode' => '999999',
                    'Ds_TransactionType' => 'A',
                    'Ds_SecurePayment' => '0',
                    'Ds_Language' => '2',
                    // undocumented
                    'Ds_MerchantData' => 'Ref: 99zz',
                    'Ds_Card_Country' => '724',
                ),
            )
        );
    }
}
