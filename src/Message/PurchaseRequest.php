<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Redsys Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://sis.redsys.es/sis/realizarPago';
    protected $testEndpoint = 'https://sis-t.redsys.es:25443/sis/realizarPago';
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
     * @param string|int Either the ISO 639-1 code to be converted, or the gateway's own numeric language code
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

    public function getData()
    {
        $this->validate('merchantId', 'terminalId', 'amount', 'currency');

        return array(
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
            'Ds_Merchant_UrlKO'              => $this->getReturnUrl(),
            'Ds_Merchant_MerchantName'       => $this->getMerchantName(),
            'Ds_Merchant_ConsumerLanguage'   => $this->getConsumerLanguage(),
            'Ds_Merchant_MerchantData'       => $this->getMerchantData(),
        );
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
