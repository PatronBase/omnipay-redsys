<?php

namespace Omnipay\Redsys\Message;

use Exception;
use SimpleXMLElement;

/**
 * Redsys Webservice Purchase Request
 */
class WebservicePurchaseRequest extends PurchaseRequest
{
    /** @var string */
    protected $liveEndpoint = "https://sis.redsys.es/sis/services/SerClsWSEntrada";
    /** @var string */
    protected $testEndpoint = "https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada";
 
    public function getData()
    {
        $this->validate('merchantId', 'terminalId', 'amount', 'currency', 'card');
        $card = $this->getCard();
        // test cards aparently don't validate
        if (!$this->getTestMode()) {
            $card->validate();
        }

        $data = array(
            'DS_MERCHANT_AMOUNT'           => $this->getAmountInteger(),
            'DS_MERCHANT_ORDER'            => $this->getTransactionId(),
            'DS_MERCHANT_MERCHANTCODE'     => $this->getMerchantId(),
            'DS_MERCHANT_CURRENCY'         => $this->getCurrencyNumeric(),  // uses ISO-4217 codes
            'DS_MERCHANT_PAN'              => $card->getNumber(),
            'DS_MERCHANT_CVV2'             => $card->getCvv(),
            'DS_MERCHANT_TRANSACTIONTYPE'  => 'A',                          // 'Traditional payment'
            'DS_MERCHANT_TERMINAL'         => $this->getTerminalId(),
            'DS_MERCHANT_EXPIRYDATE'       => $card->getExpiryDate('ym'),
            // undocumented fields
            'DS_MERCHANT_MERCHANTDATA'     => $this->getMerchantData(),
            'DS_MERCHANT_MERCHANTNAME'     => $this->getMerchantName(),
            'DS_MERCHANT_CONSUMERLANGUAGE' => $this->getConsumerLanguage(),
        );


        $request = new SimpleXMLElement('<REQUEST/>');
        $requestData = $request->addChild('DATOSENTRADA');
        foreach ($data as $tag => $value) {
            $requestData->addChild($tag, $value);
        }

        $security = new Security;

        $request->addChild('DS_SIGNATUREVERSION', Security::VERSION);
        $request->addChild('DS_SIGNATURE', $security->createSignature(
            $requestData->asXML(),
            $data['DS_MERCHANT_ORDER'],
            $this->getHmacKey()
        ));

        // keep data as nested array for method signature compatibility
        return array(
            'DATOSENTRADA'        => $data,
            'DS_SIGNATUREVERSION' => (string)$request->DS_SIGNATUREVERSION,
            'DS_SIGNATURE'        => (string)$request->DS_SIGNATURE,
        );
    }

    /**
     * Send the data
     *
     * Uses its own SOAP wrapper instead of PHP's SoapClient
     */
    public function sendData($data)
    {
        // re-create the XML
        $request = new SimpleXMLElement('<REQUEST/>');
        $requestData = $request->addChild('DATOSENTRADA');
        foreach ($data['DATOSENTRADA'] as $tag => $value) {
            $requestData->addChild($tag, $value);
        }
        $request->addChild('DS_SIGNATUREVERSION', $data['DS_SIGNATUREVERSION']);
        $request->addChild('DS_SIGNATURE', $data['DS_SIGNATURE']);

        // wrap in SOAP envelope
        $requestEnvelope = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/'>
              <soapenv:Header/>
              <soapenv:Body>
                <impl:trataPeticion xmlns:impl='http://webservice.sis.sermepa.es'>
                  <impl:datosEntrada>
                    ".htmlspecialchars($request->asXML())."
                  </impl:datosEntrada>
                </impl:trataPeticion>
              </soapenv:Body>
            </soapenv:Envelope>";

        // send the actual SOAP request
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            array('SOAPAction' => 'trataPeticion'),
            $requestEnvelope
        );

        // unwrap httpResponse into actual data as SimpleXMLElement tree
        $responseEnvelope = simplexml_load_string($httpResponse->getBody()->getContents());
        $responseData = new SimpleXMLElement(htmlspecialchars_decode(
            $responseEnvelope->children("http://schemas.xmlsoap.org/soap/envelope/")
            ->Body->children("http://webservice.sis.sermepa.es")
            ->trataPeticionResponse
            ->trataPeticionReturn
        ));


        // remove any reflected request data (this happens on SIS errors, and includes card number)
        if (isset($responseData->RECIBIDO)) {
            unset($responseData->RECIBIDO);
        }

        // convert to nested arrays (drop the 'true' to use simple objects)
        $responseData = json_decode(json_encode($responseData), true);

        return $this->response = new WebservicePurchaseResponse($this, $responseData);
    }
}
