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
            switch ($value) {
                default:
                // Spanish
                case 'es':
                    $value = '001';
                    break;
                // English
                case 'en':
                    $value = '002';
                    break;
                // Catalan - same as Valencian (010)
                case 'ca':
                    $value = '003';
                    break;
                // French
                case 'fr':
                    $value = '004';
                    break;
                // German
                case 'de':
                    $value = '005';
                    break;
                // Dutch
                case 'nl':
                    $value = '006';
                    break;
                // Italian
                case 'it':
                    $value = '007';
                    break;
                // Swedish
                case 'sv':
                    $value = '008';
                    break;
                // Portuguese
                case 'pt':
                    $value = '009';
                    break;
                // Polish
                case 'pl':
                    $value = '011';
                    break;
                // Galician
                case 'gl':
                    $value = '012';
                    break;
                // Basque
                case 'eu':
                    $value = '013';
                    break;
            }
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

        $data = array();

        // mandatory fields
        $data['Ds_Merchant_MerchantCode'] = $this->getMerchantId();
        $data['Ds_Merchant_Terminal'] = $this->getTerminalId();
        $data['Ds_Merchant_TransactionType'] = '0'; // Authorisation
        $data['Ds_Merchant_Amount'] = $this->getAmountInteger();
        $data['Ds_Merchant_Currency'] = $this->getCurrencyNumeric(); // uses ISO-4217 codes
        $data['Ds_Merchant_Order'] = $this->getTransactionId();
        $data['Ds_Merchant_MerchantUrl'] = $this->getNotifyUrl();
        // optional fields
        $data['Ds_Merchant_ProductDescription'] = $this->getDescription();
        $data['Ds_Merchant_Cardholder'] = $this->getCardholder();
        $data['Ds_Merchant_UrlOK'] = $this->getReturnUrl();
        $data['Ds_Merchant_UrlKO'] = $this->getReturnUrl();
        $data['Ds_Merchant_MerchantName'] = $this->getMerchantName();
        $data['Ds_Merchant_ConsumerLanguage'] = $this->getConsumerLanguage();
        $data['Ds_Merchant_MerchantData'] = $this->getMerchantData();

        return $data;
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
