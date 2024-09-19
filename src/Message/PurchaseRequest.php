<?php

namespace Omnipay\Redsys\Message;

use DateTime;
use DateTimeZone;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Redsys Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    /** @var string */
    protected $liveEndpoint = 'https://sis.redsys.es/sis/realizarPago';
    /** @var string */
    protected $testEndpoint = 'https://sis-t.redsys.es:25443/sis/realizarPago';
    /** @var array */
    protected static $consumerLanguages = array(
        'es' => '001', // Spanish
        'en' => '002', // English
        'ca' => '003', // Catalan - same as Valencian (010)
        'fr' => '004', // French
        'de' => '005', // German
        'nl' => '006', // Dutch
        'it' => '007', // Italian
        'sv' => '008', // Swedish
        'pt' => '009', // Portuguese
        'pl' => '011', // Polish
        'gl' => '012', // Galician
        'eu' => '013', // Basque
    );

    /** @var string 250x400 */
    const CHALLENGE_WINDOW_SIZE_250_400 = "01";
    /** @var string 390x400 */
    const CHALLENGE_WINDOW_SIZE_390_400 = "02";
    /** @var string 500x600 */
    const CHALLENGE_WINDOW_SIZE_500_600 = "03";
    /** @var string 600x400 */
    const CHALLENGE_WINDOW_SIZE_600_400 = "04";
    /** @var string Fullscreen window (default) */
    const CHALLENGE_WINDOW_SIZE_FULLSCREEN = "05";

    /** @var string No 3DS Requestor authentication occurred (i.e. cardholder logged in as guest) */
    const ACCOUNT_AUTHENTICATION_METHOD_NONE = "01";
    /** @var string Login to the cardholder account at the 3DS Requestor system using 3DS Requestor's own credentials */
    const ACCOUNT_AUTHENTICATION_METHOD_OWN_CREDENTIALS = "02";
    /** @var string Login to the cardholder account at the 3DS Requestor system using federated ID */
    const ACCOUNT_AUTHENTICATION_METHOD_FEDERATED_ID = "03";
    /** @var string Login to the cardholder account at the 3DS Requestor system using issuer credentials */
    const ACCOUNT_AUTHENTICATION_METHOD_ISSUER_CREDENTIALS = "04";
    /** @var string Login to the cardholder account at the 3DS Requestor system using third-party authentication */
    const ACCOUNT_AUTHENTICATION_METHOD_THIRD_PARTY_AUTHENTICATION = "05";
    /** @var string Login to the cardholder account at the 3DS Requestor system using FIDO Authenticator */
    const ACCOUNT_AUTHENTICATION_METHOD_FIDO = "06";

    /** @var string No account (guest check-out) */
    const CUSTOMER_ACCOUNT_CREATED_NONE = "01";
    /** @var string Created during this transaction */
    const CUSTOMER_ACCOUNT_CREATED_THIS_TRANSACTION = "02";
    /** @var string Less than 30 days */
    const CUSTOMER_ACCOUNT_CREATED_LAST_30_DAYS = "03";
    /** @var string Between 30 and 60 days */
    const CUSTOMER_ACCOUNT_CREATED_LAST_60_DAYS = "04";
    /** @var string More than 60 day */
    const CUSTOMER_ACCOUNT_CREATED_MORE_THAN_60_DAYS = "05";

    /** @var string Modified in this session */
    const CUSTOMER_ACCOUNT_MODIFIED_THIS_TRANSACTION = "01";
    /** @var string Less than 30 days */
    const CUSTOMER_ACCOUNT_MODIFIED_LAST_30_DAYS = "02";
    /** @var string Between 30 and 60 days */
    const CUSTOMER_ACCOUNT_MODIFIED_LAST_60_DAYS = "03";
    /** @var string More than 60 day */
    const CUSTOMER_ACCOUNT_MODIFIED_MORE_THAN_60_DAYS = "04";

    /** @var string Unchanged */
    const CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_NONE = "01";
    /** @var string Modified in this session */
    const CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_THIS_TRANSACTION = "02";
    /** @var string Less than 30 days */
    const CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_LAST_30_DAYS = "03";
    /** @var string Between 30 and 60 days */
    const CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_LAST_60_DAYS = "04";
    /** @var string More than 60 day */
    const CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_MORE_THAN_60_DAYS = "05";

    /** @var string No account (guest check-out) */
    const PAYMENT_METHOD_CREATED_NONE = "01";
    /** @var string Created during this transaction */
    const PAYMENT_METHOD_CREATED_THIS_TRANSACTION = "02";
    /** @var string Less than 30 days */
    const PAYMENT_METHOD_CREATED_LAST_30_DAYS = "03";
    /** @var string Between 30 and 60 days */
    const PAYMENT_METHOD_CREATED_LAST_60_DAYS = "04";
    /** @var string More than 60 day */
    const PAYMENT_METHOD_CREATED_MORE_THAN_60_DAYS = "05";

    /** @var string For the first time */
    const SHIPPING_ADDRESS_USAGE_THIS_TRANSACTION = "01";
    /** @var string Less than 30 days */
    const SHIPPING_ADDRESS_USAGE_LAST_30_DAYS = "02";
    /** @var string Between 30 and 60 days */
    const SHIPPING_ADDRESS_USAGE_LAST_60_DAYS = "03";
    /** @var string More than 60 day */
    const SHIPPING_ADDRESS_USAGE_MORE_THAN_60_DAYS = "04";

    /** @var string Electronic delivery */
    const DELIVERY_TIMEFRAME_ELECTRONIC_DELIVERY = "01";
    /** @var string Same day shipping */
    const DELIVERY_TIMEFRAME_SAME_DAY = "02";
    /** @var string Next day shipping */
    const DELIVERY_TIMEFRAME_NEXT_DAY = "03";
    /** @var string Shipping in 2 or more days */
    const DELIVERY_TIMEFRAME_2_OR_MORE_DAYS = "04";

    /** @var string Ship to cardholder's billing address */
    const SHIPPING_TO_BILLING_ADDRESS = "01";
    /** @var string Ship to another verified address on file with merchant */
    const SHIPPING_TO_ANOTHER_VERIFIED_ADDRESS = "02";
    /** @var string Ship to address that is different than the cardholder's billing address */
    const SHIPPING_DIFFERENT_BILLING_ADDRESS = "03";
    /** @var string Pick-up at local store (Store address shall be populated in shipping address fields) */
    const SHIPPING_PICK_UP = "04";
    /** @var string Digital goods (includes online services, electronic gift cards and redemption codes) */
    const SHIPPING_DIGITAL = "05";
    /** @var string Travel and Event tickets, not shipped */
    const SHIPPING_TRAVEL = "06";
    /** @var string Other (for example, Gaming, digital services not shipped, emedia subscriptions, etc.) */
    const SHIPPING_OTHER = "07";

    /** @var string Exemption due to low amount (transactions up to € 30) */
    const SCA_EXEMPTION_LOW_AMOUNT = 'LMV';
    /** @var string Exemption due to low risk */
    const SCA_EXEMPTION_LOW_RISK = 'TRA';
    /** @var string Exemption for payments identified as corporate. */
    const SCA_EXEMPTION_CORPORATE = 'COR';
    /**
     * @var string Transactions initiated by the merchant, in which there is no intervention by the customer. They are
     *             outside the scope of PSD2.
     */
    const SCA_EXEMPTION_MERCHANT_INITIATED = 'MIT';

    public function getCardholder()
    {
        return $this->getParameter('cardholder');
    }

    public function setCardholder($value)
    {
        return $this->setParameter('cardholder', $value);
    }

    public function getConsumerLanguage()
    {
        return $this->getParameter('consumerLanguage');
    }

    /**
     * Set the language presented to the consumer
     *
     * @param null|string|int Either the ISO 639-1 code to be converted, or the gateway's own numeric language code
     */
    public function setConsumerLanguage($value)
    {
        if (is_int($value)) {
            if ($value < 0 || $value > 13) {
                $value = 1;
            }
            $value = str_pad($value, 3, '0', STR_PAD_LEFT);
        } elseif (!is_numeric($value)) {
            $value = isset(self::$consumerLanguages[$value]) ? self::$consumerLanguages[$value] : '001';
        }

        return $this->setParameter('consumerLanguage', $value);
    }

    public function getHmacKey()
    {
        return $this->getParameter('hmacKey');
    }

    public function setHmacKey($value)
    {
        return $this->setParameter('hmacKey', $value);
    }

    public function getMerchantData()
    {
        return $this->getParameter('merchantData');
    }

    public function setMerchantData($value)
    {
        return $this->setParameter('merchantData', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantName()
    {
        return $this->getParameter('merchantName');
    }

    public function setMerchantName($value)
    {
        return $this->setParameter('merchantName', $value);
    }

    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    /**
     * Get the protocolVersion field
     * Corresponds to the Ds_Merchant_Emv3Ds.protocolVersion. field in Redsys documentation.
     *
     * @return null|string
     *
     */
    public function getProtocolVersion()
    {
        return $this->getParameter('protocolVersion');
    }

    /**
     * Set the protocolVersion field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.protocolVersion field
     *
     * @param null|string $value
     * @return self
     */
    public function setProtocolVersion($value)
    {
        return $this->setParameter('protocolVersion', $value);
    }

    /**
     * Get the use3ds field
     *
     * Controls the presence of the Ds_Merchant_Emv3Ds structure
     *
     * @return bool
     */
    public function getUse3DS()
    {
        return (bool) $this->getParameter('use3ds');
    }

    /**
     * Set the use3ds field
     *
     * Controls the presence of the Ds_Merchant_Emv3Ds structure
     *
     * @param bool $value
     * @return self
     */
    public function setUse3DS($value)
    {
        return $this->setParameter('use3ds', $value);
    }

    /**
     * Get the threeDSCompInd field
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSCompInd field in Redsys documentation.
     *
     * @return null|string
     *
     */
    public function getThreeDSCompInd()
    {
        return $this->getParameter('threeDSCompInd');
    }

    /**
     * Set the threeDSCompInd field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSCompInd field
     *
     * @param null|string $value
     * @return self
     */
    public function setThreeDSCompInd($value)
    {
        return $this->setParameter('threeDSCompInd', $value);
    }

    /**
     * Get the threeDSInfo field
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSInfo field in Redsys documentation.
     *
     * @return null|string
     *
     */
    public function getThreeDSInfo()
    {
        return $this->getParameter('threeDSInfo');
    }

    /**
     * Set the threeDSInfo field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSInfo field
     *
     * @param null|string $value
     * @return self
     */
    public function setThreeDSInfo($value)
    {
        return $this->setParameter('threeDSInfo', $value);
    }

    /**
     * Get the homePhoneCountryPrefix field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.homePhone.cc field
     *
     * @return null|string
     */
    public function getHomePhoneCountryPrefix()
    {
        return $this->getParameter('homePhoneCountryPrefix');
    }

    /**
     * Set the homePhoneCountryPrefix field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.homePhone.cc field
     *
     * @param null|string $value
     * @return self
     */
    public function setHomePhoneCountryPrefix($value)
    {
        return $this->setParameter('homePhoneCountryPrefix', $value);
    }

    /**
     * Get the homePhone field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.homePhone.subscriber field
     *
     * @return null|string
     */
    public function getHomePhone()
    {
        return $this->getParameter('homePhone');
    }

    /**
     * Set the homePhone field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.homePhone.subscriber field
     *
     * @param null|string $value
     * @return self
     */
    public function setHomePhone($value)
    {
        return $this->setParameter('homePhone', $value);
    }

    /**
     * Get the mobilePhoneCountryPrefix field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.mobilePhone.cc field
     *
     * @return null|string
     */
    public function getMobilePhoneCountryPrefix()
    {
        return $this->getParameter('mobilePhoneCountryPrefix');
    }

    /**
     * Set the mobilePhoneCountryPrefix field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.mobilePhone.cc field
     *
     * @param null|string $value
     * @return self
     */
    public function setMobilePhoneCountryPrefix($value)
    {
        return $this->setParameter('mobilePhoneCountryPrefix', $value);
    }

    /**
     * Get the mobilePhone field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.mobilePhone.subscriber field
     *
     * @return null|string
     */
    public function getMobilePhone()
    {
        return $this->getParameter('mobilePhone');
    }

    /**
     * Set the mobilePhone field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.mobilePhone.subscriber field
     *
     * @param null|string $value
     * @return self
     */
    public function setMobilePhone($value)
    {
        return $this->setParameter('mobilePhone', $value);
    }

    /**
     * Get the workPhoneCountryPrefix field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.workPhone.cc field
     *
     * @return null|string
     */
    public function getWorkPhoneCountryPrefix()
    {
        return $this->getParameter('workPhoneCountryPrefix');
    }

    /**
     * Set the workPhoneCountryPrefix field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.workPhone.cc field
     *
     * @param null|string $value
     * @return self
     */
    public function setWorkPhoneCountryPrefix($value)
    {
        return $this->setParameter('workPhoneCountryPrefix', $value);
    }

    /**
     * Get the workPhone field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.workPhone.subscriber field
     *
     * @return null|string
     */
    public function getWorkPhone()
    {
        return $this->getParameter('workPhone');
    }

    /**
     * Set the workPhone field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.workPhone.subscriber field
     *
     * @param null|string $value
     * @return self
     */
    public function setWorkPhone($value)
    {
        return $this->setParameter('workPhone', $value);
    }

    /**
     * Get the addressMatch field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.addrMatch field
     *
     * @return string  Either 'Y' or 'N'
     */
    public function getAddressMatch()
    {
        $match = $this->getParameter('addressMatch');

        if ($match === null) {
            $match = false;
            $card = $this->getCard();
            if ($card !== null) {
                $match = $card->getShippingAddress1() === $card->getBillingAddress1()
                    && $card->getShippingAddress2() === $card->getBillingAddress2()
                    && $card->getShippingAddress3() === $card->getBillingAddress3()
                    && $card->getShippingCity() === $card->getBillingCity()
                    && $card->getShippingPostcode() === $card->getBillingPostcode()
                    && $card->getShippingState() === $card->getBillingState()
                    && $card->getShippingCountry() === $card->getBillingCountry();
            }
        }

        return $match ? "Y" : "N";
    }

    /**
     * Set the addressMatch field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.addrMatch field
     *
     * @param null|boolean $value
     * @return self
     */
    public function setAddressMatch($value)
    {
        return $this->setParameter('addressMatch', $value);
    }

    /**
     * Get the challengeWindowSize field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.challengeWindowSize field
     *
     * @return null|string One of the self::CHALLENGE_WINDOW_SIZE_* constants
     */
    public function getChallengeWindowSize()
    {
        return $this->getParameter('challengeWindowSize');
    }

    /**
     * Set the challengeWindowSize field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.challengeWindowSize field
     *
     * @param null|string $value One of the self::CHALLENGE_WINDOW_SIZE_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setChallengeWindowSize($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::CHALLENGE_WINDOW_SIZE_250_400,
                self::CHALLENGE_WINDOW_SIZE_390_400,
                self::CHALLENGE_WINDOW_SIZE_500_600,
                self::CHALLENGE_WINDOW_SIZE_600_400,
                self::CHALLENGE_WINDOW_SIZE_FULLSCREEN,
            ]
        )) {
            return $this->setParameter('challengeWindowSize', $value);
        }
        throw new InvalidRequestException("Invalid challengeWindowSize parameter");
    }

    /**
     * Get the customerAdditionalInformation field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctID field
     *
     * @return null|string
     */
    public function getCustomerAdditionalInformation()
    {
        return $this->getParameter('customerAdditionalInformation');
    }

    /**
     * Set the customerAdditionalInformation field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctID field
     *
     * @param null|string $value
     * @return self
     */
    public function setCustomerAdditionalInformation($value)
    {
        return $this->setParameter('customerAdditionalInformation', $value);
    }

    /**
     * Get the 3DsRequestAuthenticationMethodData field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSRequestorAuthenticationInfo.threeDSReqAuthData field in Redsys
     * documentation.
     *
     * @return null|string
     */
    public function get3DsRequestAuthenticationMethodData()
    {
        return $this->getParameter('3DsRequestAuthenticationMethodData');
    }

    /**
     * Set the 3DsRequestAuthenticationMethodData field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSRequestorAuthenticationInfo.threeDSReqAuthData field in the Redsys
     * documentation.
     *
     * @param null|string $value
     * @return self
     */
    public function set3DsRequestAuthenticationMethodData($value)
    {
        return $this->setParameter('3DsRequestAuthenticationMethodData', $value);
    }

    /**
     * Get the 3DsRequestAuthenticationMethod field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSRequestorAuthenticationInfo.threeDSReqAuthMethod field in Redsys
     * documentation.
     *
     * @return null|string One of the self::ACCOUNT_AUTHENTICATION_METHOD_* constants.
     */
    public function get3DsRequestAuthenticationMethod()
    {
        return $this->getParameter('3DsRequestAuthenticationMethod');
    }

    /**
     * Set the 3DsRequestAuthenticationMethod field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSRequestorAuthenticationInfo.threeDSReqAuthMethod field in the Redsys
     * documentation.
     *
     * @param null|string $value One of the self::ACCOUNT_AUTHENTICATION_METHOD_* constants.
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function set3DsRequestAuthenticationMethod($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::ACCOUNT_AUTHENTICATION_METHOD_NONE,
                self::ACCOUNT_AUTHENTICATION_METHOD_OWN_CREDENTIALS,
                self::ACCOUNT_AUTHENTICATION_METHOD_FEDERATED_ID,
                self::ACCOUNT_AUTHENTICATION_METHOD_ISSUER_CREDENTIALS,
                self::ACCOUNT_AUTHENTICATION_METHOD_THIRD_PARTY_AUTHENTICATION,
                self::ACCOUNT_AUTHENTICATION_METHOD_FIDO,
            ]
        )) {
            return $this->setParameter('3DsRequestAuthenticationMethod', $value);
        }
        throw new InvalidRequestException("Invalid 3DsRequestAuthenticationMethod parameter");
    }

    /**
     * Get the 3DsRequestAuthenticationTime field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSRequestorAuthenticationInfo.threeDSReqAuthTimestamp field
     *
     * @return null|string
     */
    public function get3DsRequestAuthenticationTime()
    {
        return $this->getParameter('3DsRequestAuthenticationTime');
    }

    /**
     * Set the 3DsRequestAuthenticationTime field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.threeDSRequestorAuthenticationInfo.threeDSReqAuthTimestamp field
     *
     * @param null|DateTime|int $value
     * @return self
     */
    public function set3DsRequestAuthenticationTime($value)
    {
        return $this->setParameter('3DsRequestAuthenticationTime', $this->formatDateTime($value, "YmdHi"));
    }

    /**
     * Get the customerAccountCreatedIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccAgeInd field
     *
     * @return null|string One of the CUSTOMER_ACCOUNT_CREATED_* constants
     */
    public function getCustomerAccountCreatedIndicator()
    {
        return $this->getParameter('customerAccountCreatedIndicator');
    }

    /**
     * Set the customerAccountCreatedIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccAgeInd field
     *
     * @param null|string $value One of the CUSTOMER_ACCOUNT_CREATED_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setCustomerAccountCreatedIndicator($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::CUSTOMER_ACCOUNT_CREATED_NONE,
                self::CUSTOMER_ACCOUNT_CREATED_THIS_TRANSACTION,
                self::CUSTOMER_ACCOUNT_CREATED_LAST_30_DAYS,
                self::CUSTOMER_ACCOUNT_CREATED_LAST_60_DAYS,
                self::CUSTOMER_ACCOUNT_CREATED_MORE_THAN_60_DAYS,
            ]
        )) {
            return $this->setParameter('customerAccountCreatedIndicator', $value);
        }
        throw new InvalidRequestException("Invalid customerAccountCreatedIndicator parameter");
    }

    /**
     * Get the customerAccountCreatedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccDate field
     *
     * @return null|string
     */
    public function getCustomerAccountCreatedDate()
    {
        return $this->getParameter('customerAccountCreatedDate');
    }

    /**
     * Set the customerAccountCreatedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccDate field
     *
     * @param null|DateTime|int $value
     * @return self
     */
    public function setCustomerAccountCreatedDate($value)
    {
        return $this->setParameter('customerAccountCreatedDate', $this->formatDateTime($value, "Ymd"));
    }

    /**
     * Get the customerAccountChangedIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccChangeInd field
     *
     * @return null|string One of the CUSTOMER_ACCOUNT_MODIFIED_* constants
     */
    public function getCustomerAccountChangedIndicator()
    {
        return $this->getParameter('customerAccountChangedIndicator');
    }

    /**
     * Set the customerAccountChangedIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccChangeInd field
     *
     * @param null|string $value One of the CUSTOMER_ACCOUNT_MODIFIED_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setCustomerAccountChangedIndicator($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::CUSTOMER_ACCOUNT_MODIFIED_THIS_TRANSACTION,
                self::CUSTOMER_ACCOUNT_MODIFIED_LAST_30_DAYS,
                self::CUSTOMER_ACCOUNT_MODIFIED_LAST_60_DAYS,
                self::CUSTOMER_ACCOUNT_MODIFIED_MORE_THAN_60_DAYS,
            ]
        )) {
            return $this->setParameter('customerAccountChangedIndicator', $value);
        }
        throw new InvalidRequestException("Invalid customerAccountChangedIndicator parameter");
    }

    /**
     * Get the customerAccountChangedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccChange field
     *
     * @return null|string
     */
    public function getCustomerAccountChangedDate()
    {
        return $this->getParameter('customerAccountChangedDate');
    }

    /**
     * Set the customerAccountChangedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccChange field
     *
     * @param null|DateTime|int $value
     * @return self
     */
    public function setCustomerAccountChangedDate($value)
    {
        return $this->setParameter('customerAccountChangedDate', $this->formatDateTime($value, "Ymd"));
    }

    /**
     * Get the customerPasswordAgeIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccPwChangeInd field
     *
     * @return null|string One of the CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_* constants
     */
    public function getCustomerPasswordChangedIndicator()
    {
        return $this->getParameter('customerPasswordAgeIndicator');
    }

    /**
     * Set the customerPasswordAgeIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccPwChangeInd field
     *
     * @param null|string $value One of the CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setCustomerPasswordChangedIndicator($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_NONE,
                self::CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_THIS_TRANSACTION,
                self::CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_LAST_30_DAYS,
                self::CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_LAST_60_DAYS,
                self::CUSTOMER_ACCOUNT_PASSWORD_MODIFIED_MORE_THAN_60_DAYS,
            ]
        )) {
            return $this->setParameter('customerPasswordAgeIndicator', $value);
        }
        throw new InvalidRequestException("Invalid customerPasswordAgeIndicator parameter");
    }

    /**
     * Get the customerPasswordChangedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccPwChange field
     *
     * @return null|string
     */
    public function getCustomerPasswordChangedDate()
    {
        return $this->getParameter('customerPasswordChangedDate');
    }

    /**
     * Set the customerPasswordChangedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.chAccPwChange field
     *
     * @param null|DateTime|int $value
     * @return self
     */
    public function setCustomerPasswordChangedDate($value)
    {
        return $this->setParameter('customerPasswordChangedDate', $this->formatDateTime($value, "Ymd"));
    }

    /**
     * Get the customerPurchasesInLast6Months field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.nbPurchaseAccount field
     *
     * @return null|int
     */
    public function getCustomerPurchasesInLast6Months()
    {
        return $this->getParameter('customerPurchasesInLast6Months');
    }

    /**
     * Set the customerPurchasesInLast6Months field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.nbPurchaseAccount field
     *
     * @param null|int $value
     * @return self
     */
    public function setCustomerPurchasesInLast6Months($value)
    {
        return $this->setParameter('customerPurchasesInLast6Months', $value);
    }

    /**
     * Get the customerAccountCardProvisionsLast24Hours field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.provisionAttemptsDay field
     *
     * @return null|int
     */
    public function getCustomerAccountCardProvisionsLast24Hours()
    {
        return $this->getParameter('customerAccountCardProvisionsLast24Hours');
    }

    /**
     * Set the customerAccountCardProvisionsLast24Hours field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.provisionAttemptsDay field
     *
     * @param null|int $value
     * @return self
     */
    public function setCustomerAccountCardProvisionsLast24Hours($value)
    {
        return $this->setParameter('customerAccountCardProvisionsLast24Hours', $value);
    }

    /**
     * Get the customerAccountTransactionsLast24Hours field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.txnActivityDay field
     *
     * @return null|int
     */
    public function getCustomerAccountTransactionsLast24Hours()
    {
        return $this->getParameter('customerAccountTransactionsLast24Hours');
    }

    /**
     * Set the customerAccountTransactionsLast24Hours field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.txnActivityDay field
     *
     * @param null|int $value
     * @return self
     */
    public function setCustomerAccountTransactionsLast24Hours($value)
    {
        return $this->setParameter('customerAccountTransactionsLast24Hours', $value);
    }

    /**
     * Get the customerAccountTransactionsLastYear field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.txnActivityYear field
     *
     * @return null|int
     */
    public function getCustomerAccountTransactionsLastYear()
    {
        return $this->getParameter('customerAccountTransactionsLastYear');
    }

    /**
     * Set the customerAccountTransactionsLastYear field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.txnActivityYear field
     *
     * @param null|int $value
     * @return self
     */
    public function setCustomerAccountTransactionsLastYear($value)
    {
        return $this->setParameter('customerAccountTransactionsLastYear', $value);
    }

    /**
     * Get the customerPaymentMethodCreatedIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.paymentAccInd field
     *
     * @return null|string One of the PAYMENT_METHOD_CREATED_* constants
     */
    public function getCustomerPaymentMethodCreatedIndicator()
    {
        return $this->getParameter('customerPaymentMethodCreatedIndicator');
    }

    /**
     * Set the customerPaymentMethodCreatedIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.paymentAccInd field
     *
     * @param null|string $value One of the PAYMENT_METHOD_CREATED_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setCustomerPaymentMethodCreatedIndicator($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::PAYMENT_METHOD_CREATED_NONE,
                self::PAYMENT_METHOD_CREATED_THIS_TRANSACTION,
                self::PAYMENT_METHOD_CREATED_LAST_30_DAYS,
                self::PAYMENT_METHOD_CREATED_LAST_60_DAYS,
                self::PAYMENT_METHOD_CREATED_MORE_THAN_60_DAYS,
            ]
        )) {
            return $this->setParameter('customerPaymentMethodCreatedIndicator', $value);
        }
        throw new InvalidRequestException("Invalid customerPaymentMethodCreatedIndicator parameter");
    }

    /**
     * Get the customerPaymentMethodCreatedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.paymentAccAge field
     *
     * @return null|string
     */
    public function getCustomerPaymentMethodCreatedDate()
    {
        return $this->getParameter('customerPaymentMethodCreatedDate');
    }

    /**
     * Set the customerPaymentMethodCreatedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.paymentAccAge field
     *
     * @param null|DateTime|int $value
     * @return self
     */
    public function setCustomerPaymentMethodCreatedDate($value)
    {
        return $this->setParameter('customerPaymentMethodCreatedDate', $this->formatDateTime($value, "Ymd"));
    }

    /**
     * Get the shippingAddressFirstUsedIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.shipAddressUsageInd field
     *
     * @return null|string One of the SHIPPING_ADDRESS_USAGE_* constants
     */
    public function getShippingAddressFirstUsedIndicator()
    {
        return $this->getParameter('shippingAddressFirstUsedIndicator');
    }

    /**
     * Set the shippingAddressFirstUsedIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.shipAddressUsageInd field
     *
     * @param null|string $value One of the SHIPPING_ADDRESS_USAGE_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setShippingAddressFirstUsedIndicator($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::SHIPPING_ADDRESS_USAGE_THIS_TRANSACTION,
                self::SHIPPING_ADDRESS_USAGE_LAST_30_DAYS,
                self::SHIPPING_ADDRESS_USAGE_LAST_60_DAYS,
                self::SHIPPING_ADDRESS_USAGE_MORE_THAN_60_DAYS,
            ]
        )) {
            return $this->setParameter('shippingAddressFirstUsedIndicator', $value);
        }
        throw new InvalidRequestException("Invalid shippingAddressFirstUsedIndicator parameter");
    }

    /**
     * Get the shippingAddressFirstUsedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.shipAddressUsage field
     *
     * @return null|string
     */
    public function getShippingAddressFirstUsedDate()
    {
        return $this->getParameter('shippingAddressFirstUsedDate');
    }

    /**
     * Set the shippingAddressFirstUsedDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.shipAddressUsage field
     *
     * @param null|DateTime|int $value
     * @return self
     */
    public function setShippingAddressFirstUsedDate($value)
    {
        return $this->setParameter('shippingAddressFirstUsedDate', $this->formatDateTime($value, "Ymd"));
    }

    /**
     * Get the shippingNameCustomerNameMatch field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.shipNameIndicator field
     *
     * @return null|boolean
     */
    public function getShippingNameCustomerNameMatch()
    {
        return $this->getParameter('shippingNameCustomerNameMatch');
    }

    /**
     * Set the shippingNameCustomerNameMatch field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.shipNameIndicator field
     *
     * @param null|boolean $value
     * @return self
     */
    public function setShippingNameCustomerNameMatch($value)
    {
        return $this->setParameter('shippingNameCustomerNameMatch', $value);
    }

    /**
     * Get the customerHasSuspiciousActivity field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.suspiciousAccActivity field
     *
     * @return null|boolean
     */
    public function getCustomerHasSuspiciousActivity()
    {
        return $this->getParameter('customerHasSuspiciousActivity');
    }

    /**
     * Set the customerHasSuspiciousActivity field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.acctInfo.suspiciousAccActivity field
     *
     * @param null|boolean $value
     * @return self
     */
    public function setCustomerHasSuspiciousActivity($value)
    {
        return $this->setParameter('customerHasSuspiciousActivity', $value);
    }

    /**
     * Get the deliveryEmail field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.deliveryEmailAddress field
     *
     * @return null|string
     */
    public function getDeliveryEmail()
    {
        return $this->getParameter('deliveryEmail');
    }

    /**
     * Set the deliveryEmail field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.deliveryEmailAddress field
     *
     * @param null|string $value
     * @return self
     */
    public function setDeliveryEmail($value)
    {
        return $this->setParameter('deliveryEmail', $value);
    }

    /**
     * Get the deliveryTimeframeIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.deliveryTimeframe field
     *
     * @return null|string One of the DELIVERY_TIMEFRAME_* constants
     */
    public function getDeliveryTimeframeIndicator()
    {
        return $this->getParameter('deliveryTimeframeIndicator');
    }

    /**
     * Set the deliveryTimeframeIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.deliveryTimeframe field
     *
     * @param null|string $value One of the DELIVERY_TIMEFRAME_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setDeliveryTimeframeIndicator($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::DELIVERY_TIMEFRAME_ELECTRONIC_DELIVERY,
                self::DELIVERY_TIMEFRAME_SAME_DAY,
                self::DELIVERY_TIMEFRAME_NEXT_DAY,
                self::DELIVERY_TIMEFRAME_2_OR_MORE_DAYS,
            ]
        )) {
            return $this->setParameter('deliveryTimeframeIndicator', $value);
        }
        throw new InvalidRequestException("Invalid deliveryTimeframeIndicator parameter");
    }

    /**
     * Get the giftCardAmount field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.giftCardAmount field
     *
     * @return null|int
     */
    public function getGiftCardAmount()
    {
        return $this->getParameter('giftCardAmount');
    }

    /**
     * Set the giftCardAmount field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.giftCardAmount field
     *
     * @param null|int $value
     * @return self
     */
    public function setGiftCardAmount($value)
    {
        return $this->setParameter('giftCardAmount', $value);
    }

    /**
     * Get the giftCardCount field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.giftCardCount field
     *
     * @return null|int
     */
    public function getGiftCardCount()
    {
        return $this->getParameter('giftCardCount');
    }

    /**
     * Set the giftCardCount field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.giftCardCount field
     *
     * @param null|int $value
     * @return self
     */
    public function setGiftCardCount($value)
    {
        return $this->setParameter('giftCardCount', $value);
    }

    /**
     * Get the giftCardCurrency field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.giftCardCurr field
     *
     * @return null|string ISO-4217 currency code
     */
    public function getGiftCardCurrency()
    {
        return $this->getParameter('giftCardCurrency');
    }

    /**
     * Set the giftCardCurrency field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.giftCardCurr field
     *
     * @param null|string $value ISO-4217 currency code
     * @return self
     */
    public function setGiftCardCurrency($value)
    {
        return $this->setParameter('giftCardCurrency', $value);
    }

    /**
     * Get the purchasingPreOrder field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.preOrderPurchaseInd field
     *
     * @return null|boolean True if the customer is purchasing a preorder
     */
    public function getPurchasingPreOrder()
    {
        return $this->getParameter('purchasingPreOrder');
    }

    /**
     * Set the purchasingPreOrder field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.preOrderPurchaseInd field
     *
     * @param null|boolean $value True if the customer is purchasing a preorder
     * @return self
     */
    public function setPurchasingPreOrder($value)
    {
        return $this->setParameter('purchasingPreOrder', $value);
    }

    /**
     * Get the preOrderDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.preOrderDate field
     *
     * @return null|string
     */
    public function getPreOrderDate()
    {
        return $this->getParameter('preOrderDate');
    }

    /**
     * Set the preOrderDate field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.preOrderDate field
     *
     * @param null|DateTime|int $value
     * @return self
     */
    public function setPreOrderDate($value)
    {
        return $this->setParameter('preOrderDate', $this->formatDateTime($value, "Ymd"));
    }

    /**
     * Get the customerHasPurchasedProductBefore field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.reorderItemsInd field
     *
     * @return null|boolean
     */
    public function getCustomerHasPurchasedProductBefore()
    {
        return $this->getParameter('customerHasPurchasedProductBefore');
    }

    /**
     * Set the customerHasPurchasedProductBefore field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.reorderItemsInd field
     *
     * @param null|boolean $value
     * @return self
     */
    public function setCustomerHasPurchasedProductBefore($value)
    {
        return $this->setParameter('customerHasPurchasedProductBefore', $value);
    }

    /**
     * Get the shippingAddressIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.shipIndicator field
     *
     * @return null|string One of the self::SHIPPING_* constants
     */
    public function getShippingAddressIndicator()
    {
        return $this->getParameter('shippingAddressIndicator');
    }

    /**
     * Set the shippingAddressIndicator field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.shipIndicator field
     *
     * @param null|string $value One of the self::SHIPPING_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setShippingAddressIndicator($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::SHIPPING_TO_BILLING_ADDRESS,
                self::SHIPPING_TO_ANOTHER_VERIFIED_ADDRESS,
                self::SHIPPING_DIFFERENT_BILLING_ADDRESS,
                self::SHIPPING_PICK_UP,
                self::SHIPPING_DIGITAL,
                self::SHIPPING_TRAVEL,
                self::SHIPPING_OTHER,
            ]
        )) {
            return $this->setParameter('shippingAddressIndicator', $value);
        }
        throw new InvalidRequestException("Invalid shippingAddressIndicator parameter");
    }

    /**
     * Get the SCA exemption field
     *
     * Corresponds to the Ds_Merchant_Excep_Sca field
     *
     * @return null|string One of the self::SCA_EXEMPTION_* constants
     */
    public function getScaExemptionIndicator()
    {
        return $this->getParameter('scaExemptionIndicator');
    }

    /**
     * Set the SCA exemption field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.MerchantRiskIndicator.shipIndicator field
     *
     * @param null|string $value One of the self::SCA_EXEMPTION_* constants
     * @return self
     * @throws InvalidRequestException if $value is invalid.
     */
    public function setScaExemptionIndicator($value)
    {
        if (in_array(
            $value,
            [
                null,
                self::SCA_EXEMPTION_LOW_AMOUNT,
                self::SCA_EXEMPTION_LOW_RISK,
                self::SCA_EXEMPTION_CORPORATE,
                self::SCA_EXEMPTION_MERCHANT_INITIATED,
            ]
        )) {
            return $this->setParameter('scaExemptionIndicator', $value);
        }
        throw new InvalidRequestException("Invalid scaExemptionIndicator parameter");
    }

    /**
     * Override the abstract method to add requirement that it must start with 4 numeric characters
     *
     * @param string|int $value The transaction ID (merchant order) to set for the transaction
     */
    public function setTransactionId($value)
    {
        $start = substr($value, 0, 4);
        $numerics = 0;
        foreach (str_split($start) as $char) {
            if (is_numeric($char)) {
                $numerics++;
            } else {
                break;
            }
        }
        $value = str_pad(substr($start, 0, $numerics), 4, 0, STR_PAD_LEFT).substr($value, $numerics);

        parent::setTransactionId($value);
    }

    /**
     * Get the browserAcceptHeader field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserAcceptHeader field
     *
     * @return null|string
     */
    public function getBrowserAcceptHeader()
    {
        return $this->getParameter('browserAcceptHeader');
    }

    /**
     * Set the browserAcceptHeader field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserAcceptHeader field
     *
     * @param null|string $value
     * @return self
     */
    public function setBrowserAcceptHeader($value)
    {
        return $this->setParameter('browserAcceptHeader', $value);
    }

    /**
     * Get the browserColorDepth field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserColorDepth field
     *
     * @return null|int
     */
    public function getBrowserColorDepth()
    {
        return $this->getParameter('browserColorDepth');
    }

    /**
     * Set the browserColorDepth field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserColorDepth field
     *
     * @param null|int $value
     * @return self
     */
    public function setBrowserColorDepth($value)
    {
        return $this->setParameter('browserColorDepth', $value);
    }

    /**
     * Get the browserIP field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserIP field
     *
     * @return null|string
     */
    public function getBrowserIP()
    {
        return $this->getParameter('browserIP');
    }

    /**
     * Set the browserIP field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserIP field
     *
     * @param null|string $value
     * @return self
     */
    public function setBrowserIP($value)
    {
        return $this->setParameter('browserIP', $value);
    }

    /**
     * Get the browserJavaEnabled field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserJavaEnabled field
     *
     * @return null|bool
     */
    public function getBrowserJavaEnabled()
    {
        return $this->getParameter('browserJavaEnabled');
    }

    /**
     * Set the browserJavaEnabled field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserJavaEnabled field
     *
     * @param null|bool $value
     * @return self
     */
    public function setBrowserJavaEnabled($value)
    {
        return $this->setParameter('browserJavaEnabled', $value);
    }

    /**
     * Get the browserLanguage field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserLanguage field
     *
     * @return null|string
     */
    public function getBrowserLanguage()
    {
        return $this->getParameter('browserLanguage');
    }

    /**
     * Set the browserLanguage field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserLanguage field
     *
     * @param null|string $value
     * @return self
     */
    public function setBrowserLanguage($value)
    {
        return $this->setParameter('browserLanguage', $value);
    }

    /**
     * Get the browserScreenHeight field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserScreenHeight field
     *
     * @return null|int
     */
    public function getBrowserScreenHeight()
    {
        return $this->getParameter('browserScreenHeight');
    }

    /**
     * Set the browserScreenHeight field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserScreenHeight field
     *
     * @param null|int $value
     * @return self
     */
    public function setBrowserScreenHeight($value)
    {
        return $this->setParameter('browserScreenHeight', $value);
    }

    /**
     * Get the browserScreenWidth field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserScreenWidth field
     *
     * @return null|int
     */
    public function getBrowserScreenWidth()
    {
        return $this->getParameter('browserScreenWidth');
    }

    /**
     * Set the browserScreenWidth field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserScreenWidth field
     *
     * @param null|int $value
     * @return self
     */
    public function setBrowserScreenWidth($value)
    {
        return $this->setParameter('browserScreenWidth', $value);
    }

    /**
     * Get the browserTZ field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserTZ field
     *
     * @return null|int
     */
    public function getBrowserTZ()
    {
        return $this->getParameter('browserTZ');
    }

    /**
     * Set the browserTZ field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserTZ field
     *
     * @param null|int $value
     * @return self
     */
    public function setBrowserTZ($value)
    {
        return $this->setParameter('browserTZ', $value);
    }

    /**
     * Get the browserUserAgent field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserUserAgent field
     *
     * @return null|string
     */
    public function getBrowserUserAgent()
    {
        return $this->getParameter('browserUserAgent');
    }

    /**
     * Set the browserUserAgent field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.browserUserAgent field
     *
     * @param null|string $value
     * @return self
     */
    public function setBrowserUserAgent($value)
    {
        return $this->setParameter('browserUserAgent', $value);
    }

    /**
     * Get the notificationURL field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.notificationURL field
     *
     * @return null|string
     */
    public function getNotificationURL()
    {
        return $this->getParameter('notificationURL');
    }

    /**
     * Set the notificationURL field
     *
     * Corresponds to the Ds_Merchant_Emv3Ds.notificationURL field
     *
     * @param null|string $value
     * @return self
     */
    public function setNotificationURL($value)
    {
        return $this->setParameter('notificationURL', $value);
    }

    /**
     * Get the basic data fields that don't require any 3DS/SCA fields
     */
    public function getBaseData()
    {
        $this->validate('merchantId', 'terminalId', 'amount', 'currency');

        return [
            // mandatory fields
            'Ds_Merchant_MerchantCode'       => $this->getMerchantId(),
            'Ds_Merchant_Terminal'           => $this->getTerminalId(),
            'Ds_Merchant_TransactionType'    => '0',                          // Authorisation
            'Ds_Merchant_Amount'             => $this->getAmountInteger(),
            'Ds_Merchant_Currency'           => $this->getCurrencyNumeric(),  // uses ISO-4217 codes
            'Ds_Merchant_Order'              => $this->getTransactionId(),
            'Ds_Merchant_MerchantUrl'        => $this->getNotifyUrl(),
            // optional fields
            'Ds_Merchant_ProductDescription' => $this->getDescription(),
            'Ds_Merchant_Cardholder'         => $this->getCardholder(),
            'Ds_Merchant_UrlOK'              => $this->getReturnUrl(),
            'Ds_Merchant_UrlKO'              => $this->getCancelUrl(),
            'Ds_Merchant_MerchantName'       => $this->getMerchantName(),
            'Ds_Merchant_ConsumerLanguage'   => $this->getConsumerLanguage(),
            'Ds_Merchant_MerchantData'       => $this->getMerchantData(),
        ];
    }

    public function get3DSAccountInfoData()
    {
        $data = array_filter([
            'chAccAgeInd'           => $this->getCustomerAccountCreatedIndicator(),
            'chAccDate'             => $this->getCustomerAccountCreatedDate(),
            'chAccChangeInd'        => $this->getCustomerAccountChangedIndicator(),
            'chAccChange'           => $this->getCustomerAccountChangedDate(),
            'chAccPwChangeInd'      => $this->getCustomerPasswordChangedIndicator(),
            'chAccPwChange'         => $this->getCustomerPasswordChangedDate(),
            'paymentAccInd'         => $this->getCustomerPaymentMethodCreatedIndicator(),
            'paymentAccAge'         => $this->getCustomerPaymentMethodCreatedDate(),
            'shipAddressUsageInd'   => $this->getShippingAddressFirstUsedIndicator(),
            'shipAddressUsage'      => $this->getShippingAddressFirstUsedDate(),
        ]);
        // checks that can't rely on a simple filter (which could remove or add values unintentionally)
        if ($this->getShippingNameCustomerNameMatch() !== null) {
            $data['shipNameIndicator'] = $this->getShippingNameCustomerNameMatch() ? "01" : "02";
        }
        if ($this->getCustomerHasSuspiciousActivity() !== null) {
            $data['suspiciousAccActivity'] = $this->getCustomerHasSuspiciousActivity() ? "02" : "01";
        }
        if ($this->getCustomerPurchasesInLast6Months() !== null) {
            $data['nbPurchaseAccount'] = $this->getCustomerPurchasesInLast6Months();
        }
        if ($this->getCustomerAccountCardProvisionsLast24Hours() !== null) {
            $data['provisionAttemptsDay'] = $this->getCustomerAccountCardProvisionsLast24Hours();
        }
        if ($this->getCustomerAccountTransactionsLast24Hours() !== null) {
            $data['txnActivityDay'] = $this->getCustomerAccountTransactionsLast24Hours();
        }
        if ($this->getCustomerAccountTransactionsLastYear() !== null) {
            $data['txnActivityYear'] = $this->getCustomerAccountTransactionsLastYear();
        }

        return $data;
    }

    public function getMerchantRiskData()
    {
        $data = array_filter([
            'deliveryEmailAddress' => $this->getDeliveryEmail(),
            'deliveryTimeframe'    => $this->getDeliveryTimeframeIndicator(),
            'giftCardCount'        => $this->getGiftCardCount(),
            'giftCardCurr'         => $this->getGiftCardCurrency(),
            'preOrderDate'         => $this->getPreOrderDate(),
            'shipIndicator'        => $this->getShippingAddressIndicator(),
        ]);
        if ($this->getGiftCardAmount() !== null) {
            $data['giftCardAmount'] = (int) $this->getGiftCardAmount();
        }
        if ($this->getPurchasingPreOrder() !== null) {
            $data['preOrderPurchaseInd'] = $this->getPurchasingPreOrder() ? "02" : "01";
        }
        if ($this->getCustomerHasPurchasedProductBefore() !== null) {
            $data['reorderItemsInd'] = $this->getCustomerHasPurchasedProductBefore() ? "02" : "01";
        }

        return $data;
    }

    public function getData()
    {
        $data = $this->getBaseData();
        if ($this->getScaExemptionIndicator() !== null) {
            $data['Ds_Merchant_Excep_Sca'] = $this->getScaExemptionIndicator();
        }

        // only generate an EMV 3DS request if requested to
        if ($this->getUse3DS()) {
            // Validating the presence of the card under the assumption that at least an address is required
            // NOTE: normally would validate() for other submission params, but the minimum depends on bank contract
            $this->validate('card');
            $card = $this->getCard();

            $homePhone = array_filter([
                'cc'         => $this->getHomePhoneCountryPrefix(),
                'subscriber' => $this->getHomePhone(),
            ]);
            $mobilePhone = array_filter([
                'cc'         => $this->getMobilePhoneCountryPrefix(),
                'subscriber' => $this->getMobilePhone(),
            ]);
            $workPhone = array_filter([
                'cc'         => $this->getWorkPhoneCountryPrefix(),
                'subscriber' => $this->getWorkPhone(),
            ]);
            // for optional threeDSRequestorAuthenticationInfo param
            $threeDSAuthInfo = array_filter([
                'threeDSReqAuthData'      => $this->get3DsRequestAuthenticationMethodData(),
                'threeDSReqAuthMethod'    => $this->get3DsRequestAuthenticationMethod(),
                'threeDSReqAuthTimestamp' => $this->get3DsRequestAuthenticationTime(),
            ]);
            // for optional DS_MERCHANT_EMV3DS param object
            $emv3DsParameters = array_filter([
                // required parameters for v2
                'browserAcceptHeader'                => $this->getBrowserAcceptHeader(),
                'browserColorDepth'                  => $this->getBrowserColorDepth(),
                'browserIP'                          => $this->getBrowserIP(),
                'browserJavaEnabled'                 => $this->getBrowserJavaEnabled(),
                'browserLanguage'                    => $this->getBrowserLanguage(),
                'browserScreenHeight'                => $this->getBrowserScreenHeight(),
                'browserScreenWidth'                 => $this->getBrowserScreenWidth(),
                'browserTZ'                          => $this->getBrowserTZ(),
                'browserUserAgent'                   => $this->getBrowserUserAgent(),
                'notificationURL'                    => $this->getNotificationURL(),
                'protocolVersion'                    => $this->getProtocolVersion(),
                // Indicates whether the 3DSMethod has been executed. Values ​​accepted: Y= Successfully completed, N = Completed with errors, U = 3DSMethod not executed
                'threeDSCompInd'                     => $this->getThreeDSCompInd(),
                // Type of request. Possible values: CardData, AuthenticationData, ChallengeResponse
                'threeDSInfo'                        => $this->getThreeDSInfo(),
                'threeDSServerTransID'               => $this->getTransactionId(),
                // optional parameters for v2
                'cardholderName'                     => $this->getCardholder(),
                // in Redsys DS_MERCHANT_EMV3DS table as 'E-mail'
                'email'                              => $card->getEmail(),
                'homePhone'                          => $homePhone,
                'mobilePhone'                        => $mobilePhone,
                'workPhone'                          => $workPhone,
                'shipAddrLine1'                      => $card->getShippingAddress1(),
                'shipAddrLine2'                      => $card->getShippingAddress2(),
                'shipAddrLine3'                      => $card->getShippingAddress3(),
                'shipAddrCity'                       => $card->getShippingCity(),
                'shipAddrPostCode'                   => $card->getShippingPostcode(),
                'shipAddrState'                      => $card->getShippingState(),
                'shipAddrCountry'                    => $card->getShippingCountry(),
                'billAddrLine1'                      => $card->getBillingAddress1(),
                'billAddrLine2'                      => $card->getBillingAddress2(),
                'billAddrLine3'                      => $card->getBillingAddress3(),
                'billAddrCity'                       => $card->getBillingCity(),
                'billAddrPostCode'                   => $card->getBillingPostcode(),
                'billAddrState'                      => $card->getBillingState(),
                'billAddrCountry'                    => $card->getBillingCountry(),
                'addrMatch'                          => $this->getAddressMatch(),
                'challengeWindowSize'                => $this->getChallengeWindowSize(),
                'acctID'                             => $this->getCustomerAdditionalInformation(),
                'threeDSRequestorAuthenticationInfo' => $threeDSAuthInfo,
            ]);

            $acctInfo = $this->get3DSAccountInfoData();
            if ($acctInfo !== []) {
                $emv3DsParameters['acctInfo'] = $acctInfo;
            }

            $merchantRiskIndicator = $this->getMerchantRiskData();
            if ($merchantRiskIndicator !== []) {
                $emv3DsParameters['merchantRiskIndicator'] = $merchantRiskIndicator;
            }

            if ($emv3DsParameters !== []) {
                $data['Ds_Merchant_Emv3Ds'] = $emv3DsParameters;
            }
        }

        return $data;
    }

    public function sendData($data)
    {
        $security = new Security;

        $encoded_data = $security->encodeMerchantParameters($data);

        $response_data = array(
            'Ds_SignatureVersion'   => Security::VERSION,
            'Ds_MerchantParameters' => $encoded_data,
            'Ds_Signature'          => $security->createSignature(
                $encoded_data,
                $data['Ds_Merchant_Order'],
                $this->getHmacKey()
            ),
        );

        return $this->response = new PurchaseResponse($this, $response_data);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * Convert a DateTime or timestamp to a formatted date.
     *
     * @param DateTime|int|null $date   The date to format.
     * @param string            $format The format to use.
     *
     * @return string|null The formatted date, or null if date isn't a timestamp or DateTime object.
     */
    protected function formatDateTime($date, $format)
    {
        if (is_int($date)) {
            return (new DateTime('@'.$date))->format($format);
        } elseif ($date instanceof DateTime) {
            return $date->setTimezone(new DateTimeZone('UTC'))->format($format);
        }
    }
}
