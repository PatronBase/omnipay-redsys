<?php

namespace Omnipay\Redsys\Message;

use DateTime;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /** @var PurchaseRequest */
    private $request;
    
    /** @var mixed[] */
    protected $requiredRequestParams = [
      'merchantId'       => '999008881',
      'terminalId'       => '871',
      'amount'           => '1.45',
      'currency'         => 'EUR',
    ];

    /** @var mixed[] */
    protected $fullBaseParams = [];

    /** @var mixed[] */
    protected $full3DSParams = [];

    public function setUp(): void
    {
        $this->fullBaseParams = $this->requiredRequestParams + [
            'clientIp'         => '192.0.0.0',
            'transactionId'    => '123abc',
            'hmacKey'          => 'Mk9m98IfEblmPfrpsawt7BmxObt98Jev',
            'description'      => 'My sales items',
            'cardholder'       => 'J Smith',
            'cancelUrl'        => 'https://www.example.com/cancel',
            'notifyUrl'        => 'https://www.example.com/notify',
            'returnUrl'        => 'https://www.example.com/return',
            'merchantName'     => 'My Store',
            'consumerLanguage' => 'en',
            'merchantData'     => 'Ref: 99zz',
            'directPayment'    => false,
        ];

        $this->full3DSParams = $this->fullBaseParams + [
            'use3DS'               => true,
            'protocolVersion'      => '2.1.0',
            'threeDSCompInd'       => 'U',
            'threeDSInfo'          => 'CardData',
            'threeDSServerTransID' => '12345',
            'browserAcceptHeader'  => 'text/html,application',
            'browserColorDepth'    => '24',
            'browserJavaEnabled'   => true,
            'browserLanguage'      => 'en-GB',
            'browserScreenHeight'  => '1000',
            'browserScreenWidth'   => '1200',
            'browserTZ'            => '2',
            'browserUserAgent'     => 'Mozilla/5.0',
            'card'                 => new CreditCard([
                'email' => "test@example.net",
                'shippingAddress1' => "Ship 1 Test",
                'shippingAddress2' => "Ship 2 Test",
                'shippingAddress3' => "Ship 3 Test",
                'shippingCity'     => "Ship 4 City",
                'shippingPostcode' => "Ship 5 Postcode",
                'shippingState'    => "Ship 6 State",
                'shippingCountry'  => "Ship 7 Country",
                'billingAddress1'  => "Bill 1 Test",
                'billingAddress2'  => "Bill 2 Test",
                'billingAddress3'  => "Bill 3 Test",
                'billingCity'      => "Bill 4 City",
                'billingPostcode'  => "Bill 5 Postcode",
                'billingState'     => "Bill 6 State",
                'billingCountry'   => "Bill 7 Country",
            ]),
            'addressMatch'                             => false,
            'challengeWindowSize'                      => PurchaseRequest::CHALLENGE_WINDOW_SIZE_250_400,
            'customerAdditionalInformation'            => "Extra info",
            'homePhoneCountryPrefix'                   => 123,
            'homePhone'                                => 456789,
            'mobilePhoneCountryPrefix'                 => 234,
            'mobilePhone'                              => 567890,
            'workPhoneCountryPrefix'                   => 345,
            'workPhone'                                => 6789012,
            '3DsRequestAuthenticationMethodData'       => "xyz",
            '3DsRequestAuthenticationMethod'           => PurchaseRequest::ACCOUNT_AUTHENTICATION_METHOD_OWN_CREDENTIALS,
            '3DsRequestAuthenticationTime'             => 1484089611,
            'customerAccountCreatedIndicator'          => PurchaseRequest::CUSTOMER_ACCOUNT_CREATED_LAST_30_DAYS,
            'customerAccountCreatedDate'               => 1360632822,
            'customerAccountChangedIndicator'          => PurchaseRequest::CUSTOMER_ACCOUNT_MODIFIED_THIS_TRANSACTION,
            'customerAccountChangedDate'               => 1237176033,
            'customerPasswordChangedIndicator'         => PurchaseRequest::CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_NONE,
            'customerPasswordChangedDate'              => 1113719244,
            'customerPurchasesInLast6Months'           => 0,
            'customerAccountCardProvisionsLast24Hours' => 4,
            'customerAccountTransactionsLast24Hours'   => 0,
            'customerAccountTransactionsLastYear'      => 89,
            'customerPaymentMethodCreatedIndicator'    => PurchaseRequest::PAYMENT_METHOD_CREATED_THIS_TRANSACTION,
            'customerPaymentMethodCreatedDate'         => 990262455,
            'shippingAddressFirstUsedIndicator'        => PurchaseRequest::SHIPPING_ADDRESS_USAGE_LAST_30_DAYS,
            'shippingAddressFirstUsedDate'             => 866805666,
            'shippingNameCustomerNameMatch'            => true,
            'customerHasSuspiciousActivity'            => false,
            'deliveryEmail'                            => "example@example.com",
            'deliveryTimeframeIndicator'               => PurchaseRequest::DELIVERY_TIMEFRAME_ELECTRONIC_DELIVERY,
            'giftCardAmount'                           => 456,
            'giftCardCount'                            => 567,
            'giftCardCurrency'                         => "NZD",
            'purchasingPreOrder'                       => true,
            'preOrderDate'                             => 743348877,
            'customerHasPurchasedProductBefore'        => true,
            'shippingAddressIndicator'                 => PurchaseRequest::SHIPPING_DIGITAL,
        ];

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($this->full3DSParams);
    }

    public function testGetBaseData()
    {
        $data = $this->request->getBaseData();

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
        $this->assertSame('https://www.example.com/cancel', $data['Ds_Merchant_UrlKO']);
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
        $this->request->setConsumerLanguage(99); // invalid, forces 1
        $this->assertSame('001', $this->request->getConsumerLanguage());
        $this->request->setConsumerLanguage('en');
        $this->assertSame('002', $this->request->getConsumerLanguage());
    }

    public function testSetDirectPayment()
    {
        // starts false in base test data
        $this->assertFalse($this->request->getDirectPayment());
        // valid, but corrects case
        $this->request->setDirectPayment("moto");
        $this->assertSame('MOTO', $this->request->getDirectPayment());
        // valid (effectively unsets)
        $this->request->setDirectPayment(null);
        $this->assertNull($this->request->getDirectPayment());
        // valid
        $this->request->setDirectPayment(true);
        $this->assertTrue($this->request->getDirectPayment());
        $this->request->setDirectPayment(false);
        $this->assertFalse($this->request->getDirectPayment());
        // valid, but converts to bool
        $this->request->setDirectPayment("true");
        $this->assertTrue($this->request->getDirectPayment());
        $this->request->setDirectPayment("false");
        $this->assertFalse($this->request->getDirectPayment());
        // invalid, forces back to null
        $this->request->setDirectPayment(100);
        $this->assertNull($this->request->getDirectPayment());
    }

    public function testGet3DSAccountInfoData()
    {
        $data = $this->request->get3DSAccountInfoData();
        $this->assertArrayHasKey("chAccAgeInd", $data);
        $this->assertSame("03", $data['chAccAgeInd']);
        $this->assertArrayHasKey("chAccDate", $data);
        $this->assertSame("20130212", $data['chAccDate']);
        $this->assertArrayHasKey("chAccChangeInd", $data);
        $this->assertSame("01", $data['chAccChangeInd']);
        $this->assertArrayHasKey("chAccChange", $data);
        $this->assertSame("20090316", $data['chAccChange']);
        $this->assertArrayHasKey("chAccPwChangeInd", $data);
        $this->assertSame("01", $data['chAccPwChangeInd']);
        $this->assertArrayHasKey("chAccPwChange", $data);
        $this->assertSame("20050417", $data['chAccPwChange']);
        $this->assertArrayHasKey("nbPurchaseAccount", $data);
        $this->assertSame(0, $data['nbPurchaseAccount']);
        $this->assertArrayHasKey("provisionAttemptsDay", $data);
        $this->assertSame(4, $data['provisionAttemptsDay']);
        $this->assertArrayHasKey("txnActivityDay", $data);
        $this->assertSame(0, $data['txnActivityDay']);
        $this->assertArrayHasKey("txnActivityYear", $data);
        $this->assertSame(89, $data['txnActivityYear']);
        $this->assertArrayHasKey("paymentAccInd", $data);
        $this->assertSame("02", $data['paymentAccInd']);
        $this->assertArrayHasKey("paymentAccAge", $data);
        $this->assertSame("20010519", $data['paymentAccAge']);
        $this->assertArrayHasKey("shipAddressUsageInd", $data);
        $this->assertSame("02", $data['shipAddressUsageInd']);
        $this->assertArrayHasKey("shipAddressUsage", $data);
        $this->assertSame("19970620", $data['shipAddressUsage']);
        $this->assertArrayHasKey("shipNameIndicator", $data);
        $this->assertSame("01", $data['shipNameIndicator']);
        $this->assertArrayHasKey("suspiciousAccActivity", $data);
        $this->assertSame("01", $data['suspiciousAccActivity']);
    }

    public function testGetMerchantRiskData()
    {
        $data = $this->request->getMerchantRiskData();
        $this->assertArrayHasKey("deliveryEmailAddress", $data);
        $this->assertSame("example@example.com", $data['deliveryEmailAddress']);
        $this->assertArrayHasKey("deliveryTimeframe", $data);
        $this->assertSame("01", $data['deliveryTimeframe']);
        $this->assertArrayHasKey("giftCardAmount", $data);
        $this->assertSame(456, $data['giftCardAmount']);
        $this->assertArrayHasKey("giftCardCount", $data);
        $this->assertSame(567, $data['giftCardCount']);
        $this->assertArrayHasKey("giftCardCurr", $data);
        $this->assertSame("NZD", $data['giftCardCurr']);
        $this->assertArrayHasKey("preOrderPurchaseInd", $data);
        $this->assertSame("02", $data['preOrderPurchaseInd']);
        $this->assertArrayHasKey("preOrderDate", $data);
        $this->assertSame("19930722", $data['preOrderDate']);
        $this->assertArrayHasKey("reorderItemsInd", $data);
        $this->assertSame("02", $data['reorderItemsInd']);
        $this->assertArrayHasKey("shipIndicator", $data);
        $this->assertSame("05", $data['shipIndicator']);
    }

    public function testFormatDateTime()
    {
        // int
        $this->request->set3DsRequestAuthenticationTime(1484089611);
        $this->assertSame("201701102306", $this->request->get3DsRequestAuthenticationTime());
        // NZDT DateTime
        $this->request->set3DsRequestAuthenticationTime(new DateTime('2017-01-11T12:06:51+13:00'));
        $this->assertSame("201701102306", $this->request->get3DsRequestAuthenticationTime());
        // UTC DateTime
        $this->request->set3DsRequestAuthenticationTime(new DateTime('2017-01-10T23:06:51Z'));
        $this->assertSame("201701102306", $this->request->get3DsRequestAuthenticationTime());
        // null
        $this->request->set3DsRequestAuthenticationTime(null);
        $this->assertNull($this->request->get3DsRequestAuthenticationTime());
        // invalid format e.g. string
        $this->request->set3DsRequestAuthenticationTime("invalid");
        $this->assertNull($this->request->get3DsRequestAuthenticationTime());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertArrayHasKey("Ds_Merchant_Emv3Ds", $data);
        $this->assertArrayHasKey("cardholderName", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("J Smith", $data['Ds_Merchant_Emv3Ds']['cardholderName']);
        $this->assertArrayHasKey("Ds_Merchant_Emv3Ds", $data);
        $this->assertArrayHasKey("email", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("test@example.net", $data['Ds_Merchant_Emv3Ds']['email']);
        $this->assertArrayHasKey("shipAddrLine1", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Ship 1 Test", $data['Ds_Merchant_Emv3Ds']['shipAddrLine1']);
        $this->assertArrayHasKey("shipAddrLine2", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Ship 2 Test", $data['Ds_Merchant_Emv3Ds']['shipAddrLine2']);
        $this->assertArrayHasKey("shipAddrLine3", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Ship 3 Test", $data['Ds_Merchant_Emv3Ds']['shipAddrLine3']);
        $this->assertArrayHasKey("shipAddrCity", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Ship 4 City", $data['Ds_Merchant_Emv3Ds']['shipAddrCity']);
        $this->assertArrayHasKey("shipAddrPostCode", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Ship 5 Postcode", $data['Ds_Merchant_Emv3Ds']['shipAddrPostCode']);
        $this->assertArrayHasKey("shipAddrState", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Ship 6 State", $data['Ds_Merchant_Emv3Ds']['shipAddrState']);
        $this->assertArrayHasKey("shipAddrCountry", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Ship 7 Country", $data['Ds_Merchant_Emv3Ds']['shipAddrCountry']);
        $this->assertArrayHasKey("billAddrLine1", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Bill 1 Test", $data['Ds_Merchant_Emv3Ds']['billAddrLine1']);
        $this->assertArrayHasKey("billAddrLine2", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Bill 2 Test", $data['Ds_Merchant_Emv3Ds']['billAddrLine2']);
        $this->assertArrayHasKey("billAddrLine3", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Bill 3 Test", $data['Ds_Merchant_Emv3Ds']['billAddrLine3']);
        $this->assertArrayHasKey("billAddrCity", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Bill 4 City", $data['Ds_Merchant_Emv3Ds']['billAddrCity']);
        $this->assertArrayHasKey("billAddrPostCode", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Bill 5 Postcode", $data['Ds_Merchant_Emv3Ds']['billAddrPostCode']);
        $this->assertArrayHasKey("billAddrState", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Bill 6 State", $data['Ds_Merchant_Emv3Ds']['billAddrState']);
        $this->assertArrayHasKey("billAddrCountry", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Bill 7 Country", $data['Ds_Merchant_Emv3Ds']['billAddrCountry']);
        $this->assertArrayHasKey("addrMatch", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("N", $data['Ds_Merchant_Emv3Ds']['addrMatch']);
        $this->assertArrayHasKey("challengeWindowSize", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("01", $data['Ds_Merchant_Emv3Ds']['challengeWindowSize']);
        $this->assertArrayHasKey("acctID", $data['Ds_Merchant_Emv3Ds']);
        $this->assertSame("Extra info", $data['Ds_Merchant_Emv3Ds']['acctID']);
        $this->assertArrayHasKey("homePhone", $data['Ds_Merchant_Emv3Ds']);
        $this->assertArrayHasKey("cc", $data['Ds_Merchant_Emv3Ds']['homePhone']);
        $this->assertSame(123, $data['Ds_Merchant_Emv3Ds']['homePhone']['cc']);
        $this->assertArrayHasKey("subscriber", $data['Ds_Merchant_Emv3Ds']['homePhone']);
        $this->assertSame(456789, $data['Ds_Merchant_Emv3Ds']['homePhone']['subscriber']);
        $this->assertArrayHasKey("mobilePhone", $data['Ds_Merchant_Emv3Ds']);
        $this->assertArrayHasKey("cc", $data['Ds_Merchant_Emv3Ds']['mobilePhone']);
        $this->assertSame(234, $data['Ds_Merchant_Emv3Ds']['mobilePhone']['cc']);
        $this->assertArrayHasKey("subscriber", $data['Ds_Merchant_Emv3Ds']['mobilePhone']);
        $this->assertSame(567890, $data['Ds_Merchant_Emv3Ds']['mobilePhone']['subscriber']);
        $this->assertArrayHasKey("workPhone", $data['Ds_Merchant_Emv3Ds']);
        $this->assertArrayHasKey("cc", $data['Ds_Merchant_Emv3Ds']['workPhone']);
        $this->assertSame(345, $data['Ds_Merchant_Emv3Ds']['workPhone']['cc']);
        $this->assertArrayHasKey("subscriber", $data['Ds_Merchant_Emv3Ds']['workPhone']);
        $this->assertSame(6789012, $data['Ds_Merchant_Emv3Ds']['workPhone']['subscriber']);
        $this->assertArrayHasKey("threeDSRequestorAuthenticationInfo", $data['Ds_Merchant_Emv3Ds']);
        $this->assertArrayHasKey("threeDSReqAuthData", $data['Ds_Merchant_Emv3Ds']['threeDSRequestorAuthenticationInfo']);
        $this->assertSame("xyz", $data['Ds_Merchant_Emv3Ds']['threeDSRequestorAuthenticationInfo']['threeDSReqAuthData']);
        $this->assertArrayHasKey("threeDSReqAuthMethod", $data['Ds_Merchant_Emv3Ds']['threeDSRequestorAuthenticationInfo']);
        $this->assertSame("02", $data['Ds_Merchant_Emv3Ds']['threeDSRequestorAuthenticationInfo']['threeDSReqAuthMethod']);
        $this->assertArrayHasKey("threeDSReqAuthTimestamp", $data['Ds_Merchant_Emv3Ds']['threeDSRequestorAuthenticationInfo']);
        $this->assertSame("201701102306", $data['Ds_Merchant_Emv3Ds']['threeDSRequestorAuthenticationInfo']['threeDSReqAuthTimestamp']);
        $this->assertArrayHasKey("acctInfo", $data['Ds_Merchant_Emv3Ds']);
        $this->assertArrayHasKey("chAccAgeInd", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("03", $data['Ds_Merchant_Emv3Ds']['acctInfo']['chAccAgeInd']);
        $this->assertArrayHasKey("chAccDate", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("20130212", $data['Ds_Merchant_Emv3Ds']['acctInfo']['chAccDate']);
        $this->assertArrayHasKey("chAccChangeInd", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("01", $data['Ds_Merchant_Emv3Ds']['acctInfo']['chAccChangeInd']);
        $this->assertArrayHasKey("chAccChange", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("20090316", $data['Ds_Merchant_Emv3Ds']['acctInfo']['chAccChange']);
        $this->assertArrayHasKey("chAccPwChangeInd", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("01", $data['Ds_Merchant_Emv3Ds']['acctInfo']['chAccPwChangeInd']);
        $this->assertArrayHasKey("chAccPwChange", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("20050417", $data['Ds_Merchant_Emv3Ds']['acctInfo']['chAccPwChange']);
        $this->assertArrayHasKey("nbPurchaseAccount", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame(0, $data['Ds_Merchant_Emv3Ds']['acctInfo']['nbPurchaseAccount']);
        $this->assertArrayHasKey("provisionAttemptsDay", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame(4, $data['Ds_Merchant_Emv3Ds']['acctInfo']['provisionAttemptsDay']);
        $this->assertArrayHasKey("txnActivityDay", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame(0, $data['Ds_Merchant_Emv3Ds']['acctInfo']['txnActivityDay']);
        $this->assertArrayHasKey("txnActivityYear", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame(89, $data['Ds_Merchant_Emv3Ds']['acctInfo']['txnActivityYear']);
        $this->assertArrayHasKey("paymentAccInd", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("02", $data['Ds_Merchant_Emv3Ds']['acctInfo']['paymentAccInd']);
        $this->assertArrayHasKey("paymentAccAge", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("20010519", $data['Ds_Merchant_Emv3Ds']['acctInfo']['paymentAccAge']);
        $this->assertArrayHasKey("shipAddressUsageInd", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("02", $data['Ds_Merchant_Emv3Ds']['acctInfo']['shipAddressUsageInd']);
        $this->assertArrayHasKey("shipAddressUsage", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("19970620", $data['Ds_Merchant_Emv3Ds']['acctInfo']['shipAddressUsage']);
        $this->assertArrayHasKey("shipNameIndicator", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("01", $data['Ds_Merchant_Emv3Ds']['acctInfo']['shipNameIndicator']);
        $this->assertArrayHasKey("suspiciousAccActivity", $data['Ds_Merchant_Emv3Ds']['acctInfo']);
        $this->assertSame("01", $data['Ds_Merchant_Emv3Ds']['acctInfo']['suspiciousAccActivity']);
        $this->assertArrayHasKey("merchantRiskIndicator", $data['Ds_Merchant_Emv3Ds']);
        $this->assertArrayHasKey("deliveryEmailAddress", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame("example@example.com", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['deliveryEmailAddress']);
        $this->assertArrayHasKey("deliveryTimeframe", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame("01", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['deliveryTimeframe']);
        $this->assertArrayHasKey("giftCardAmount", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame(456, $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['giftCardAmount']);
        $this->assertArrayHasKey("giftCardCount", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame(567, $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['giftCardCount']);
        $this->assertArrayHasKey("giftCardCurr", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame("NZD", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['giftCardCurr']);
        $this->assertArrayHasKey("preOrderPurchaseInd", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame("02", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['preOrderPurchaseInd']);
        $this->assertArrayHasKey("preOrderDate", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame("19930722", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['preOrderDate']);
        $this->assertArrayHasKey("reorderItemsInd", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame("02", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['reorderItemsInd']);
        $this->assertArrayHasKey("shipIndicator", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']);
        $this->assertSame("05", $data['Ds_Merchant_Emv3Ds']['merchantRiskIndicator']['shipIndicator']);

        $this->assertArrayNotHasKey("Ds_Merchant_Excep_Sca", $data);
    }

    public function testUse3DS()
    {
        $this->request->setUse3DS(false);
        $this->request->setScaExemptionIndicator(PurchaseRequest::SCA_EXEMPTION_LOW_AMOUNT);
        $data = $this->request->getData();

        $this->assertArrayNotHasKey("Ds_Merchant_Emv3Ds", $data);
        $this->assertArrayHasKey("Ds_Merchant_Excep_Sca", $data);
        $this->assertSame("LMV", $data['Ds_Merchant_Excep_Sca']);
    }

    public function testAddressMatchSuccessByInference()
    {
        $this->request->initialize($this->fullBaseParams + [
            'use3DS' => true,
            'card' => new CreditCard([
                'address1' => "Ship Bill 1 Test",
            ]),
        ]);
        $data = $this->request->getData();
        $this->assertSame("Y", $data['Ds_Merchant_Emv3Ds']['addrMatch']);
    }

    public function testAddressMatchFailureByInference()
    {
        $this->request->initialize($this->fullBaseParams + [
            'use3DS' => true,
            'card' => new CreditCard([
                'address1' => "Ship Bill 1 Test",
                'shippingAddress2' => "Ship 2 Test",
            ]),
        ]);
        $data = $this->request->getData();
        $this->assertSame("N", $data['Ds_Merchant_Emv3Ds']['addrMatch']);
    }

    public function testMinimal3DSGetData()
    {
        $this->request->initialize($this->requiredRequestParams + [
            'use3DS' => true,
            'card' => new CreditCard([
                'email' => "test@example.net",
                'shippingAddress1' => "Ship 1 Test",
                'shippingAddress2' => "Ship 2 Test",
                'shippingAddress3' => "Ship 3 Test",
                'shippingCity' => "Ship 4 City",
                'shippingPostcode' => "Ship 5 Postcode",
                'shippingState' => "Ship 6 State",
                'shippingCountry' => "Ship 7 Country",
            ]),
            'homePhoneCountryPrefix' => 123,
            'homePhone' => 456789,
        ]);
        $data = $this->request->getData();

        $this->assertSame("test@example.net", $data['Ds_Merchant_Emv3Ds']['email']);
        $this->assertSame("Ship 1 Test", $data['Ds_Merchant_Emv3Ds']['shipAddrLine1']);
        $this->assertSame("Ship 2 Test", $data['Ds_Merchant_Emv3Ds']['shipAddrLine2']);
        $this->assertSame("Ship 3 Test", $data['Ds_Merchant_Emv3Ds']['shipAddrLine3']);
        $this->assertSame("Ship 4 City", $data['Ds_Merchant_Emv3Ds']['shipAddrCity']);
        $this->assertSame("Ship 5 Postcode", $data['Ds_Merchant_Emv3Ds']['shipAddrPostCode']);
        $this->assertSame("Ship 6 State", $data['Ds_Merchant_Emv3Ds']['shipAddrState']);
        $this->assertSame("Ship 7 Country", $data['Ds_Merchant_Emv3Ds']['shipAddrCountry']);
        $this->assertSame(123, $data['Ds_Merchant_Emv3Ds']['homePhone']['cc']);
        $this->assertSame(456789, $data['Ds_Merchant_Emv3Ds']['homePhone']['subscriber']);
        // inferred
        $this->assertSame("N", $data['Ds_Merchant_Emv3Ds']['addrMatch']);
        // null checks
        $this->assertNull($this->request->getCardholder());
        $missing_keys = [
            "billAddrLine1",
            "billAddrLine2",
            "billAddrLine3",
            "billAddrCity",
            "billAddrPostCode",
            "billAddrState",
            "billAddrCountry",
            "challengeWindowSize",
            "acctID",
            "mobilePhone",
            "workPhone",
            "threeDSRequestorAuthenticationInfo",
            "threeDSReqAuthTimestamp",
            "acctInfo",
            "merchantRiskIndicator",
        ];
        foreach ($missing_keys as $key) {
            $this->assertArrayNotHasKey($key, $data['Ds_Merchant_Emv3Ds']);
        }
    }

    public function testChallengeWindowSizeInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setChallengeWindowSize("invalid");
    }

    public function test3DsRequestAuthenticationMethodInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->set3DsRequestAuthenticationMethod("invalid");
    }

    public function testCustomerAccountCreatedIndicatorInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setCustomerAccountCreatedIndicator("invalid");
    }

    public function testCustomerAccountChangedIndicatorInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setCustomerAccountChangedIndicator("invalid");
    }

    public function testCustomerPasswordChangedIndicatorInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setCustomerPasswordChangedIndicator("invalid");
    }

    public function testCustomerPaymentMethodCreatedIndicatorInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setCustomerPaymentMethodCreatedIndicator("invalid");
    }

    public function testShippingAddressFirstUsedIndicatorInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setShippingAddressFirstUsedIndicator("invalid");
    }

    public function testDeliveryTimeframeIndicatorInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setDeliveryTimeframeIndicator("invalid");
    }

    public function testShippingAddressIndicatorInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setShippingAddressIndicator("invalid");
    }

    public function testScaExemptionIndicatorInvalidData()
    {
        $this->expectException(InvalidRequestException::class);
        $this->request->setScaExemptionIndicator("invalid");
    }
}
