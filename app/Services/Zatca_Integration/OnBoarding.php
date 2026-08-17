<?php

namespace Allam\Zatca;

use Allam\Zatca\Invoice\ComplianceSteps;
use Exception;
use GuzzleHttp\Exception\ClientException;

/**
 * A class defines first step for zatca phase two integration (get zatca authorization)
 */
class OnBoarding
{
    private $env;

    private $language = 'en';

    private $emailAddress;

    private $certificateTemplateName;

    private $commonName;

    private $countryCode;

    private $organizationUnitName;

    private $organizationName;

    private $egsSerialNumber;

    private $vatNumber;

    private $invoiceType;

    private $registeredAddress;

    private $businessCategory;

    private $authOtp = null;

    private $certificate = null;

    private $certificateSecret = null;

    private $certificateRequestID = null;

    private $productionCertificate = null;

    private $productionCertificateSecret = null;

    private $productionCertificateRequestID = null;

    private $privateKey;

    private $csrKey;

    /**
     * Set zatca environment
     */
    public function setZatcaEnv($env)
    {
        if (! in_array($env, ZatcaConfig::getEnvironments())) {
            throw new Exception('Zatca environment is required');
        }

        $this->certificateTemplateName = ZatcaConfig::getCertificateTemplates($env);
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
     * Set zatca auth otp
     */
    public function setAuthOtp($authOtp)
    {
        if (is_null($authOtp) || empty($authOtp)) {
            throw new Exception('Zatca Otp is required');
        }

        $this->authOtp = $authOtp;

        return $this;
    }

    /**
     * Set email address
     */
    public function setEmailAddress($emailAddress)
    {
        if (is_null($emailAddress) || empty($emailAddress)) {
            throw new Exception('Email Address is required');
        }

        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Set common name of company
     */
    public function setCommonName($commonName)
    {
        if (is_null($commonName) || empty($commonName)) {
            throw new Exception('Common Name is required');
        }

        $this->commonName = $commonName;

        return $this;
    }

    /**
     * Set country code of company
     */
    public function setCountryCode($countryCode)
    {
        if (is_null($countryCode) || empty($countryCode)) {
            throw new Exception('Country Code is required');
        }

        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Set organization unit name of company
     */
    public function setOrganizationUnitName($organizationUnitName)
    {
        if (is_null($organizationUnitName) || empty($organizationUnitName)) {
            throw new Exception('Organization Unit Name is required');
        }

        $this->organizationUnitName = $organizationUnitName;

        return $this;
    }

    /**
     * Set organization name of company
     */
    public function setOrganizationName($organizationName)
    {
        if (is_null($organizationName) || empty($organizationName)) {
            throw new Exception('Organization Name is required');
        }

        $this->organizationName = $organizationName;

        return $this;
    }

    /**
     * Set egs serial number of company
     */
    public function setEgsSerialNumber($egsSerialNumber)
    {
        if (is_null($egsSerialNumber) || empty($egsSerialNumber)) {
            throw new Exception('Egs Serial Number is required');
        }

        $this->egsSerialNumber = $egsSerialNumber;

        return $this;
    }

    /**
     * Set vat number of company
     */
    public function setVatNumber($vatNumber)
    {
        if (is_null($vatNumber) || empty($vatNumber)) {
            throw new Exception('Vat Number is required');
        }

        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * Set invoice type of company
     */
    public function setInvoiceType($invoiceType)
    {
        if (! in_array($invoiceType, ZatcaConfig::getInvoiceTypes())) {
            throw new Exception('Invoice Type is required');
        }

        $this->invoiceType = $invoiceType;

        return $this;
    }

    /**
     * Set registered address of company
     */
    public function setRegisteredAddress($registeredAddress)
    {
        if (is_null($registeredAddress) || empty($registeredAddress)) {
            throw new Exception('Registered Address is required');
        }

        $this->registeredAddress = $registeredAddress;

        return $this;
    }

    /**
     * Set business category of company
     */
    public function setBusinessCategory($businessCategory)
    {
        if (is_null($businessCategory) || empty($businessCategory)) {
            throw new Exception('Business Category is required');
        }

        $this->businessCategory = $businessCategory;

        return $this;
    }

    /**
     * Generate zatca required settings into array
     */
    private function getSettings()
    {
        return [
            'emailAddress' => $this->emailAddress,
            'certificateTemplateName' => $this->certificateTemplateName,
            'commonName' => $this->commonName,
            'countryCode' => $this->countryCode,
            'organizationUnitName' => $this->organizationUnitName,
            'organizationName' => $this->organizationName,
            'egsSerialNumber' => $this->egsSerialNumber,
            'vatNumber' => $this->vatNumber,
            'invoiceType' => $this->invoiceType,
            'registeredAddress' => $this->registeredAddress,
            'businessCategory' => $this->businessCategory,
        ];
    }

    /**
     * Generate zatca configuration template
     */
    public function generateConfigTemplate()
    {
        $settingsData = $this->getSettings();

        $emailAddress = $settingsData['emailAddress'];
        $certificateTemplateName = $settingsData['certificateTemplateName'];
        $commonName = $settingsData['commonName'];
        $countryCode = $settingsData['countryCode'];
        $organizationUnitName = $settingsData['organizationUnitName'];
        $organizationName = $settingsData['organizationName'];
        $egsSerialNumber = $settingsData['egsSerialNumber'];
        $vatNumber = $settingsData['vatNumber'];
        $invoiceType = $settingsData['invoiceType'];
        $registeredAddress = $settingsData['registeredAddress'];
        $businessCategory = $settingsData['businessCategory'];

        return "
            oid_section = OIDs
            [ OIDs ]
            certificateTemplateName= 1.3.6.1.4.1.311.20.2

            [ req ]
            default_bits 	= 2048
            emailAddress 	= {$emailAddress}
            req_extensions	= v3_req
            x509_extensions 	= v3_ca
            prompt = no
            default_md = sha256
            req_extensions = req_ext
            distinguished_name = dn

            [ v3_req ]
            basicConstraints = CA:FALSE
            keyUsage = digitalSignature, nonRepudiation, keyEncipherment

            [req_ext]
            certificateTemplateName = ASN1:PRINTABLESTRING:{$certificateTemplateName}
            subjectAltName = dirName:alt_names

            [ v3_ca ]

            # Extensions for a typical CA

            # PKIX recommendation.

            subjectKeyIdentifier = hash

            authorityKeyIdentifier = keyid:always,issuer:always

            [ dn ]
            CN = {$commonName}  				                    # Common Name
            C = {$countryCode}							            # Country Code e.g SA
            OU = {$organizationUnitName}							# Organization Unit Name
            O = {$organizationName}							        # Organization Name

            [alt_names]
            SN = {$egsSerialNumber}				                    # EGS Serial Number 1-ABC|2-PQR|3-XYZ
            UID = {$vatNumber}						                # Organization Identifier (VAT Number)
            title = {$invoiceType}								    # Invoice Type
            registeredAddress = {$registeredAddress}  	 			# Address
            businessCategory = {$businessCategory}					# Business Category
        ";
    }

    /**
     * Generate openssl configuration array
     */
    public function generateOpensslConfiguration($tempFilePath)
    {
        return [
            'config' => $tempFilePath,
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1',
        ];
    }

    /**
     * Generate private key
     */
    public function generatePrivateKey()
    {
        $tempFile = tmpfile();
        fwrite($tempFile, $this->generateConfigTemplate());
        fseek($tempFile, 0);
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        $this->privateKey = openssl_pkey_new($this->generateOpensslConfiguration($tempFilePath));

        if (! $this->privateKey) {
            throw new Exception('ERROR: Fail to generate private key. -> '.openssl_error_string());
        }

        fclose($tempFile);
    }

    /**
     * Export private key
     */
    public function exportPrivateKey()
    {
        $tempFile = tmpfile();
        fwrite($tempFile, $this->generateConfigTemplate());
        fseek($tempFile, 0);
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        $privateKey = $this->privateKey;
        openssl_pkey_export($privateKey, $exportedString, null, $this->generateOpensslConfiguration($tempFilePath));

        fclose($tempFile);

        return $exportedString;
    }

    /**
     * Export public key
     */
    public function exportPublicKey()
    {
        $privateKey = $this->privateKey;
        $keyDetails = openssl_pkey_get_details($privateKey);

        return $keyDetails['key'];
    }

    /**
     * Generate csr key
     */
    public function generateCsrKey()
    {
        $this->generatePrivateKey();

        $settingsData = $this->getSettings();
        $privateKey = $this->privateKey;

        $commonName = $settingsData['commonName'];
        $countryCode = $settingsData['countryCode'];
        $organizationUnitName = $settingsData['organizationUnitName'];
        $organizationName = $settingsData['organizationName'];

        $tempFile = tmpfile();
        fwrite($tempFile, $this->generateConfigTemplate());
        fseek($tempFile, 0);
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        $dn = [
            'commonName' => $commonName,
            'organizationalUnitName' => $organizationUnitName,
            'organizationName' => $organizationName,
            'countryName' => $countryCode,
        ];
        $csrKey = openssl_csr_new($dn, $privateKey, ['digest_alg' => 'sha256', 'req_extensions' => 'req_ext', 'curve_name' => 'secp256k1', 'config' => $tempFilePath]);

        fclose($tempFile);

        return $csrKey;
    }

    /**
     * Export csr key
     */
    public function exportCsrKey()
    {
        openssl_csr_export($this->csrKey, $exportedString);

        return $exportedString;
    }

    /**
     * Build zatca onboarding api request
     */
    public function request($body, $isProduction = false)
    {

        if (count($body) == 0) {
            throw new Exception('Body data is must\'t be empty !');
        }
        $url = ZatcaConfig::BaseUrl($this->env);
        $url .= ($isProduction) ? '/production/csids' : '/compliance';
        $options['json'] = $body;
        $options['headers'] = [
            'Content-Type' => 'application/json',
            'Accept-Language' => $this->language,
            'Accept-Version' => 'V2',
            'Accept' => 'application/json',
        ];

        if (! $isProduction) {
            $options['headers']['otp'] = $this->authOtp;
        }

        $client = new \GuzzleHttp\Client(['verify' => false]);

        if ($isProduction) {
            if (empty($this->certificate) || empty($this->certificateSecret)) {
                throw new Exception('Zatca Basic Auth is required');
            }
            $options['auth'] = [$this->certificate, $this->certificateSecret];
        }

        $request = null;
        $response = null;
        $statusCode = 0;

        try {
            $request = $client->request('POST', $url, $options);
            $statusCode = $request->getStatusCode();
            $response = json_decode($request->getBody()->getContents());
            if ($isProduction) {
                $this->productionCertificate = $response->binarySecurityToken;
                $this->productionCertificateSecret = $response->secret;
                $this->productionCertificateRequestID = $response->requestID;
            } else {
                $this->certificate = $response->binarySecurityToken;
                $this->certificateSecret = $response->secret;
                $this->certificateRequestID = $response->requestID;
            }

            return ['success' => true, 'response' => $response];
        } catch (ClientException $exception) {
            $statusCode = $exception->getResponse()->getStatusCode();
            $response = json_decode($exception->getResponse()->getBody()->getContents());

            return ['success' => false, 'response' => $response];
        } finally {
            $decodedResponse = json_decode(json_encode($response), true);

            $data['parentable_id'] = $this->vatNumber;
            $data['model'] = 'merchant vat registration number';
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
     * Get zatca authorization
     */
    public function getAuthorization()
    {

        if (is_null($this->authOtp) || empty($this->authOtp)) {
            throw new Exception('Zatca Otp is required');
        }
        $this->csrKey = $this->generateCsrKey();

        $body = [
            'csr' => base64_encode($this->exportCsrKey()),
        ];

        $complianceRequest = $this->request($body);

        if ($complianceRequest['success']) {
            $body = [
                'compliance_request_id' => $this->certificateRequestID,
            ];

            (new ComplianceSteps(
                $this->certificate,
                $this->certificateSecret,
                $this->exportPrivateKey(),
                $this->vatNumber,
                $this->invoiceType,
                $this->env,
            ))->sendComplianceSteps();

            $productionRequest = $this->request($body, true);

            return $this->handleResponse($productionRequest);
        } else {
            return $this->handleResponse($complianceRequest);
        }
    }

    /**
     * Get Final results
     */
    public function getFinalResults()
    {
        return [
            'complianceCertificate' => $this->certificate,
            'complianceSecret' => $this->certificateSecret,
            'complianceRequestID' => $this->certificateRequestID,
            'productionCertificate' => $this->productionCertificate,
            'productionCertificateSecret' => $this->productionCertificateSecret,
            'productionCertificateRequestID' => $this->productionCertificateRequestID,
            'privateKey' => base64_encode($this->exportPrivateKey()),
            'publicKey' => base64_encode($this->exportPublicKey()),
            'csrKey' => base64_encode($this->exportCsrKey()),
            'configData' => base64_encode($this->generateConfigTemplate()),
        ];
    }

    /**
     * Handle http response
     */
    public function handleResponse($response)
    {
        if ($response['success']) {
            return [
                'success' => true,
                'message' => $response['response']->{'dispositionMessage'},
                'data' => $this->getFinalResults(),
            ];
        } else {
            if (isset($response['response']->{'errors'}) && count($response['response']->{'errors'}) > 0) {
                return [
                    'success' => false,
                    'message' => isset($response['response']->{'errors'}[0]->{'message'}) ? $response['response']->{'errors'}[0]->{'message'} : $response['response']->{'errors'}[0],
                    'data' => null,
                ];
            } elseif (isset($response['response']->{'code'}) && $response['response']->{'code'} == 'Invalid-OTP') {
                return [
                    'success' => false,
                    'message' => $response['response']->{'message'},
                    'data' => null,
                ];
            } elseif (isset($response['response']->{'code'}) && $response['response']->{'code'} == 'Missing-ComplianceSteps') {
                return [
                    'success' => false,
                    'message' => $response['response']->{'message'},
                    'data' => null,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $response,
                    'data' => null,
                ];
            }
        }
    }
}
