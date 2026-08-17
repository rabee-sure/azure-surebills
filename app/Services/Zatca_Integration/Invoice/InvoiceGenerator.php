<?php

namespace Allam\Zatca\Invoice;

use Allam\Zatca\Cert509XParser;
use Allam\Zatca\QRCodeString;
use Allam\Zatca\ZatcaConfig;
use Allam\Zatca\ZatcaLog;
use chillerlan\QRCode\QRCode;
use Exception;
use GuzzleHttp\Exception\ClientException;

/**
 * A class defines zatca phase two invoice generator
 */
class InvoiceGenerator
{
    private $profileID = 'reporting:1.0';

    private $invoiceNumber;

    private $invoiceUuid;

    private $invoiceIssueDate;

    private $invoiceIssueTime;

    private $invoiceType;

    private $invoiceDocumentType;

    private $invoiceCurrencyCode;

    private $invoiceTaxCurrencyCode;

    private $billingReference = null;

    private $AdditionalDocumentReference;

    private $pih;

    private $supplier;

    private $client = null;

    private $delivery;

    private $paymentType;

    private $allowanceCharges;

    private $returnReason = null;

    private $legalMonetaryTotal;

    private $taxesTotal;

    private $taxSubTotal;

    private $invoiceLines;

    private $timestamp;

    private $certificateEncoded;

    private $certificateSecret;

    private $privateKey;

    private $invoiceDigitalSignature = null;

    private $env;

    private $language = 'en';

    private $qrImage;

    public function __construct()
    {
        $this->timestamp = (new \DateTime)->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * Set invoice number
     */
    public function setInvoiceNumber($invoiceNumber)
    {

        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Set invoice uuid
     */
    public function setInvoiceUuid($invoiceUuid)
    {

        $this->invoiceUuid = $invoiceUuid;

        return $this;
    }

    /**
     * Set invoice issue date
     */
    public function setInvoiceIssueDate($invoiceIssueDate)
    {

        $this->invoiceIssueDate = $invoiceIssueDate;

        return $this;
    }

    /**
     * Set invoice issue time
     */
    public function setInvoiceIssueTime($invoiceIssueTime)
    {

        $this->invoiceIssueTime = $invoiceIssueTime;

        return $this;
    }

    /**
     * Define invoice simplified or standard by (invoiceType) & define is it normal invocie or debit note or credit note by (invoiceDocumentType)
     */
    public function setInvoiceType($invoiceType, $invoiceDocumentType)
    {

        $this->invoiceType = $invoiceType;
        $this->invoiceDocumentType = $invoiceDocumentType;

        return $this;
    }

    /**
     * Set invoice currency code
     */
    public function setInvoiceCurrencyCode($invoiceCurrencyCode)
    {

        $this->invoiceCurrencyCode = $invoiceCurrencyCode;

        return $this;
    }

    /**
     * Set invoice tax currency code
     */
    public function setInvoiceTaxCurrencyCode($invoiceTaxCurrencyCode)
    {

        $this->invoiceTaxCurrencyCode = $invoiceTaxCurrencyCode;

        return $this;
    }

    /**
     * Set invoice billing reference
     */
    public function setInvoiceBillingReference($billingReference)
    {
        if ($this->invoiceDocumentType != '388') {
            $this->billingReference = $billingReference;
        }

        return $this;
    }

    /**
     * Set invoice additional document reference
     */
    public function setInvoiceAdditionalDocumentReference($AdditionalDocumentReference)
    {
        $this->AdditionalDocumentReference = $AdditionalDocumentReference;

        return $this;
    }

    /**
     * Set invoice previous hash
     */
    public function setInvoicePIH($pih)
    {

        $this->pih = $pih;

        return $this;
    }

    /**
     * Set invoice supplier
     */
    public function setInvoiceSupplier($supplier)
    {

        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Set invoice client
     */
    public function setInvoiceClient($client)
    {

        $this->client = $client;

        return $this;
    }

    /**
     * Set invoice delivery
     */
    public function setInvoiceDelivery($delivery)
    {

        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Set invoice payment type
     */
    public function setInvoicePaymentType($paymentType)
    {

        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Set invoice allowance charges
     */
    public function setInvoiceAllowanceCharges(...$allowanceCharges)
    {

        $this->allowanceCharges = $allowanceCharges;

        return $this;
    }

    /**
     * Set invoice return reason
     */
    public function setInvoiceReturnReason($returnReason)
    {
        if ($this->invoiceDocumentType != '388') {
            $this->returnReason = $returnReason;
        }

        return $this;
    }

    /**
     * Get payment means element
     */
    public function getPaymentMeansElement()
    {
        $paymentMeans = [];
        array_push($paymentMeans, $this->paymentType->getElement());
        if ($this->returnReason) {
            array_push($paymentMeans, $this->returnReason->getElement());
        }

        return $paymentMeans;
    }

    /**
     * Set invoice legal monetary total
     */
    public function setInvoiceLegalMonetaryTotal($legalMonetaryTotal)
    {

        $this->legalMonetaryTotal = $legalMonetaryTotal;

        return $this;
    }

    /**
     * Set invoice tax totals
     */
    public function setInvoiceTaxesTotal($taxesTotal)
    {

        $this->taxesTotal = $taxesTotal;

        return $this;
    }

    /**
     * Set invoice tax sub totals
     */
    public function setInvoiceTaxSubTotal(...$taxSubTotal)
    {
        array_unshift($taxSubTotal, $this->taxesTotal->getElement());
        $this->taxSubTotal = $taxSubTotal;

        return $this;
    }

    /**
     * Set zatca environment
     */
    public function setZatcaEnv($env)
    {
        if (! in_array($env, ZatcaConfig::getEnvironments())) {
            throw new Exception('Zatca environment is required');
        }

        $this->env = $env;

        return $this;
    }

    /**
     * Set zatca response messages language
     */
    public function setZatcaLang($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get invoice Hash
     */
    public function getInvoiceHashBaseEncoded()
    {
        $xml = (new XmlBuilder)
            ->build($this->getElement())
            ->generateAsText();

        $xml = str_replace('<?xml version="1.0"?>', '', $xml);
        $xml = str_replace('<cbc:elementToRemoved></cbc:elementToRemoved>', '', $xml);

        return base64_encode(hash('sha256', trim($xml), true));
    }

    /**
     * Set invoice lines
     */
    public function setInvoiceLines(...$invoiceLines)
    {

        $this->invoiceLines = $invoiceLines;

        return $this;
    }

    /**
     * Load certificate parser
     */
    public function certificateParser()
    {
        return (new Cert509XParser)
            ->setCertificateEncoded($this->certificateEncoded)
            ->setPrivateKeyEncoded($this->privateKey)
            ->setCertificateSecret($this->certificateSecret);
    }

    /**
     * Get invoice digital signature
     */
    public function getInvoiceDigitalSignature()
    {
        if (is_null($this->invoiceDigitalSignature)) {
            openssl_sign($this->getInvoiceHashBaseEncoded(), $signature, $this->certificateParser()->getPrivateKeyDecoded(), 'sha256');
            $this->invoiceDigitalSignature = base64_encode($signature);
        }

        return $this->invoiceDigitalSignature;
    }

    /**
     * Get invoice qrcode element
     */
    public function getInvoiceQrCodeElement()
    {
        $qrCodeString = new QRCodeString([
            $this->supplier->getVatName(),
            $this->supplier->getVatNumber(),
            (string) $this->invoiceIssueDate.'T'.(string) $this->invoiceIssueTime,
            number_format($this->legalMonetaryTotal->getTaxInclusiveAmount(), 2, '.', ''),
            number_format($this->taxesTotal->getTaxTotal(), 2, '.', ''),
            $this->getInvoiceHashBaseEncoded(),
            $this->getInvoiceDigitalSignature(),
            $this->certificateParser()->getCertificatePublicKeyEncoded(),
            $this->certificateParser()->getCertificateSignature(),
        ]);

        $qrImage = (new QRCode)->render($qrCodeString->toBase64());
        $this->setQrImage($qrImage);

        return (new Qr)
            ->setQrCode($qrCodeString->toBase64())
            ->getElement();
    }

    /**
     * Set Qr Image
     */
    public function setQrImage($qrImage)
    {

        $this->qrImage = $qrImage;

        return $this;
    }

    /**
     * Get invoice signature element
     */
    public function getInvoiceSignatureElement()
    {
        return (new Signature)
            ->getElement();
    }

    /**
     * Set invoice signed properties hash base64 encoded
     */
    public function GetSignedPropertiesHashEncoded()
    {
        $ublDefaults = (new UBLExtensions)
            ->setSigningTimestamp($this->timestamp)
            ->setCertificateHash($this->certificateParser()->getCertificateHashEncoded())
            ->setCertificateIssuer($this->certificateParser()->getCertificateIssuerName())
            ->setCertificateSerialNumber($this->certificateParser()->getCertificateSerialNumber())
            ->getElement();

        $xml = (new XmlBuilder)
            ->build($this->getElement($ublDefaults))
            ->generateAsText();

        // Creating an XMLReader
        $reader = new \XMLReader;
        $reader->xml($xml);

        // Opening a reader
        while ($reader->read()) {
            if ($reader->name == 'xades:QualifyingProperties' && $reader->nodeType === \XmlReader::ELEMENT) {
                $signedProperties = $reader->readInnerXml();
            }
        }

        // Closing the reader
        $reader->close();

        return base64_encode(hash('sha256', trim($signedProperties), false));
    }

    /**
     * Get Signed invoice
     */
    public function getSignedInvoiceEncoded()
    {
        $uBLExtensions = (new UBLExtensions)
            ->setInvoiceHash($this->getInvoiceHashBaseEncoded())
            ->setSignedPropertiesHash($this->GetSignedPropertiesHashEncoded())
            ->setDigitalSignature($this->getInvoiceDigitalSignature())
            ->setCertificateValue($this->certificateParser()->getCertificateDecoded())
            ->setSigningTimestamp($this->timestamp)
            ->setCertificateHash($this->certificateParser()->getCertificateHashEncoded())
            ->setCertificateIssuer($this->certificateParser()->getCertificateIssuerName())
            ->setCertificateSerialNumber($this->certificateParser()->getCertificateSerialNumber())
            ->getElement();

        $xml = (new XmlBuilder)
            ->build($this->getElement($uBLExtensions, true))
            ->generateAsText();

        return trim(base64_encode($xml));
    }

    /**
     * Send document to zatca
     */
    public function sendDocument($isProduction = false)
    {
        $options['json'] = [
            'invoiceHash' => $this->getInvoiceHashBaseEncoded(),
            'uuid' => $this->invoiceUuid,
            'invoice' => $this->getSignedInvoiceEncoded(),
        ];

        $url = ZatcaConfig::BaseUrl($this->env);
        if ($isProduction) {
            if ($this->invoiceType == '0200000') {
                $url .= '/invoices/reporting/single';
            } else {
                $url .= '/invoices/clearance/single';
            }
        } else {
            $url .= '/compliance/invoices';
        }

        $client = new \GuzzleHttp\Client(['verify' => false]);

        $options['headers'] = [
            'Content-Type' => 'application/json',
            'Accept-Language' => $this->language,
            'Accept-Version' => 'V2',
            'Clearance-Status' => '1',
            'Accept' => 'application/json',
        ];

        if (empty($this->certificateEncoded) || empty($this->certificateSecret)) {
            throw new Exception('Zatca Basic Auth is required');
        }
        $options['auth'] = [$this->certificateEncoded, $this->certificateSecret];

        $request = null;
        $response = null;
        $statusCode = 0;

        try {
            $request = $client->request('POST', $url, $options);
            $statusCode = $request->getStatusCode();
            $response = json_decode($request->getBody()->getContents());
            $xml = '';
            if ($this->env != 'developer-portal' && $this->invoiceType != '0200000') {
                $xml = isset($response->clearedInvoice) ? $response->clearedInvoice : null;
            } else {
                $xml = $this->getSignedInvoiceEncoded();
            }

            return ['success' => true, 'response' => $response, 'hash' => $this->getInvoiceHashBaseEncoded(), 'xml' => $xml, 'qrImage' => $this->qrImage];
        } catch (ClientException $exception) {
            $statusCode = $exception->getResponse()->getStatusCode();
            $response = json_decode($exception->getResponse()->getBody()->getContents());

            return ['success' => false, 'response' => $response];
        } finally {
            $decodedResponse = json_decode(json_encode($response), true);

            $model = null;

            switch ($this->invoiceDocumentType) {
                case '388':
                    $model = 'App\Models\Bill';
                    break;

                case '383':
                    $model = 'App\Models\Bill';
                    break;

                case '381':
                    $model = 'App\Models\RefundedBill';
                    break;
                default:
                    // code...
                    break;
            }

            $data['parentable_id'] = $this->invoiceUuid;
            $data['model'] = $model;
            $data['payload'] = $options;
            $data['api'] = $url;
            $data['response'] = $decodedResponse;
            $data['response_code'] = $statusCode;
            $data['reporting_status'] = (isset($decodedResponse['reportingStatus'])) ? $decodedResponse['reportingStatus'] : null;
            $data['clearance_status'] = (isset($decodedResponse['clearanceStatus'])) ? $decodedResponse['clearanceStatus'] : null;
            if (isset($decodedResponse['dispositionMessage'])) {
                $data['disposition_message'] = $decodedResponse['dispositionMessage'];
            } elseif (isset($decodedResponse['DispositionMessage'])) {
                $data['disposition_message'] = $decodedResponse['DispositionMessage'];
            } else {
                $data['disposition_message'] = null;
            }
            $data['status'] = (isset($decodedResponse['status'])) ? $decodedResponse['status'] : null;
            $data['qrSellert_status'] = (isset($decodedResponse['qrSellertStatus'])) ? $decodedResponse['qrSellertStatus'] : null;
            $data['qrBuyert_status'] = (isset($decodedResponse['qrBuyertStatus'])) ? $decodedResponse['qrBuyertStatus'] : null;

            (new ZatcaLog)->responseLog($data);
        }
    }

    /**
     * Set certificate encoded
     */
    public function setCertificateEncoded($certificateEncoded)
    {
        $this->certificateEncoded = $certificateEncoded;

        return $this;
    }

    /**
     * Set certificate secret
     */
    public function setCertificateSecret($certificateSecret)
    {
        $this->certificateSecret = $certificateSecret;

        return $this;
    }

    /**
     * Set private key
     */
    public function setPrivateKeyEncoded($privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * The getElement method is called during xml writing.
     */
    public function getElement($uBLExtensions = [], $forSigning = false)
    {
        return [
            [
                'name' => 'Invoice',
                'value' => null,
                'namespaced' => false,
                'namespace' => null,
                'prefix' => null,
                'attributes' => [
                    [
                        'name' => 'xmlns',
                        'value' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                    [
                        'name' => 'xmlns:cac',
                        'value' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                    [
                        'name' => 'xmlns:cbc',
                        'value' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                    [
                        'name' => 'xmlns:ext',
                        'value' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2',
                        'namespaced' => false,
                        'namespace' => null,
                        'prefix' => null,
                    ],
                ],
                'childs' => array_merge((count($uBLExtensions) > 0) ? [$uBLExtensions] : [null], [
                    ($forSigning) ? null : [
                        'name' => 'elementToRemoved',
                        'value' => '',
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    [
                        'name' => 'ProfileID',
                        'value' => $this->profileID,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    [
                        'name' => 'ID',
                        'value' => $this->invoiceNumber,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    [
                        'name' => 'UUID',
                        'value' => $this->invoiceUuid,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    [
                        'name' => 'IssueDate',
                        'value' => $this->invoiceIssueDate,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    [
                        'name' => 'IssueTime',
                        'value' => $this->invoiceIssueTime,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    [
                        'name' => 'InvoiceTypeCode',
                        'value' => $this->invoiceDocumentType,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                        'attributes' => [
                            [
                                'name' => 'name',
                                'value' => $this->invoiceType,
                                'namespaced' => false,
                                'namespace' => null,
                                'prefix' => null,
                            ],
                        ],
                    ],
                    [
                        'name' => 'DocumentCurrencyCode',
                        'value' => $this->invoiceCurrencyCode,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    [
                        'name' => 'TaxCurrencyCode',
                        'value' => $this->invoiceTaxCurrencyCode,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    (! is_null($this->billingReference)) ? [
                        'name' => 'BillingReference',
                        'value' => null,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cac',
                        'childs' => [
                            $this->billingReference->getElement(),
                        ],
                    ] : null,
                    [
                        'name' => 'AdditionalDocumentReference',
                        'value' => null,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cac',
                        'childs' => $this->AdditionalDocumentReference->getElement(),
                    ],
                    [
                        'name' => 'AdditionalDocumentReference',
                        'value' => null,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cac',
                        'childs' => $this->pih->getElement(),
                    ],
                    ($forSigning) ? null : [
                        'name' => 'elementToRemoved',
                        'value' => '',
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    ($forSigning) ? null : [
                        'name' => 'elementToRemoved',
                        'value' => '',
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cbc',
                    ],
                    (! $forSigning) ? null : [
                        'name' => 'AdditionalDocumentReference',
                        'value' => null,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cac',
                        'childs' => $this->getInvoiceQrCodeElement(),
                    ],
                    (! $forSigning) ? null : [
                        'name' => 'Signature',
                        'value' => null,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cac',
                        'childs' => $this->getInvoiceSignatureElement(),
                    ],
                    [
                        'name' => 'AccountingSupplierParty',
                        'value' => null,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cac',
                        'childs' => [
                            $this->supplier->getElement(),
                        ],
                    ],
                    [
                        'name' => 'AccountingCustomerParty',
                        'value' => (is_null($this->client)) ? ' ' : null,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cac',
                        'childs' => (! is_null($this->client)) ? [
                            $this->client->getElement(),
                        ] : null,
                    ],
                    [
                        'name' => 'Delivery',
                        'value' => null,
                        'namespaced' => true,
                        'namespace' => null,
                        'prefix' => 'cac',
                        'childs' => [
                            $this->delivery->getElement(),
                        ],
                    ]],
                    [
                        [
                            'name' => 'PaymentMeans',
                            'value' => null,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cac',
                            'childs' => $this->getPaymentMeansElement(),
                        ],
                    ],
                    $this->allowanceCharges,
                    [
                        [
                            'name' => 'TaxTotal',
                            'value' => null,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cac',
                            'childs' => [
                                $this->taxesTotal->getElement(),
                            ],
                        ],
                        [
                            'name' => 'TaxTotal',
                            'value' => null,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cac',
                            'childs' => $this->taxSubTotal,
                        ],
                        [
                            'name' => 'LegalMonetaryTotal',
                            'value' => null,
                            'namespaced' => true,
                            'namespace' => null,
                            'prefix' => 'cac',
                            'childs' => $this->legalMonetaryTotal->getElement(),
                        ],
                    ], $this->invoiceLines),

            ],
        ];
    }
}
