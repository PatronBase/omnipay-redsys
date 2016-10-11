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
     * @expectedException         Omnipay\Common\Exception\InvalidResponseException
     * @expectedExceptionMessage  Invalid response from payment gateway (no data)
     */
    public function testPurchaseInvalidNoReturnCode()
    {
        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'OPERACION' => array(),
            )
        );
    }

    /**
     * @expectedException         Omnipay\Common\Exception\InvalidResponseException
     * @expectedExceptionMessage  Invalid response from payment gateway (no data)
     */
    public function testPurchaseInvalidNoTransactionData()
    {
        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => '0',
            )
        );
    }

    /**
     * @expectedException         Omnipay\Common\Exception\InvalidResponseException
     * @expectedExceptionMessage  Invalid response from payment gateway (SIS0042)
     */
    public function testPurchaseIntegrationError()
    {
        $this->response = new WebservicePurchaseResponse(
            $this->getMockRequest(),
            array(
                'CODIGO' => 'SIS0042',
            )
        );
    }

    /**
     * @expectedException         Omnipay\Common\Exception\InvalidResponseException
     * @expectedExceptionMessage  Invalid response from payment gateway
     */
    public function testCompletePurchaseInvalidNoOrder()
    {
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
     * @expectedException         Omnipay\Common\Exception\InvalidResponseException
     * @expectedExceptionMessage  Invalid response from payment gateway (missing data)
     */
    public function testCompletePurchaseInvalidMissingData()
    {
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
     * @expectedException         Omnipay\Common\Exception\InvalidResponseException
     * @expectedExceptionMessage  Invalid response from payment gateway (signature mismatch)
     */
    public function testPurchaseBadSignature()
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
