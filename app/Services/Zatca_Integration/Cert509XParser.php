<?php

namespace Allam\Zatca;

use phpseclib3\File\X509;

/**
 * A class defines certificate parser
 */
class Cert509XParser
{
    private $certificateEncoded;

    private $certificateSecret;

    private $privateKey;

    private $x509;

    public function __construct()
    {
        $this->x509 = new X509;
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
     * Get certificate decoded value
     */
    public function getCertificateDecoded()
    {
        return base64_decode($this->certificateEncoded);
    }

    /**
     * Get private key decoded
     */
    public function getPrivateKeyDecoded()
    {
        return base64_decode($this->privateKey);
    }

    /**
     * Get certificate with headers and footers
     */
    public function getCertificate()
    {
        return "-----BEGIN CERTIFICATE-----\r\n".$this->getCertificateDecoded()."\r\n-----END CERTIFICATE-----";
    }

    /**
     * Get certificate hash base64 encoded
     */
    public function getCertificateHashEncoded()
    {
        return base64_encode(hash('sha256', $this->getCertificateDecoded(), false));
    }

    /**
     * Get certificate signature
     */
    public function getCertificateSignature()
    {
        $certOut = $this->x509->loadX509($this->GetCertificate());
        $signature = unpack('H*', $certOut['signature'])['1'];

        return pack('H*', substr($signature, 2));
    }

    /**
     * Get certificate public key base64 encoded
     */
    public function getCertificatePublicKeyEncoded()
    {
        $this->x509->loadX509($this->GetCertificate());
        $publicKey = $this->x509->getPublicKey();
        $publicKey = str_replace('-----BEGIN PUBLIC KEY-----', '', $publicKey);
        $publicKey = str_replace('-----END PUBLIC KEY-----', '', $publicKey);

        return base64_decode($publicKey);
    }

    /**
     * Get certificate serial number
     */
    public function getCertificateSerialNumber()
    {
        $certOut = $this->x509->loadX509($this->GetCertificate());

        return $certOut['tbsCertificate']['serialNumber']->toString();
    }

    /**
     * Get certificate issuer name
     */
    public function getCertificateIssuerName()
    {
        $this->x509->loadX509($this->GetCertificate());
        $issuer_names = [];
        $issuer_info = $this->x509->getIssuerDN(X509::DN_OPENSSL);

        foreach ($issuer_info as $key_parent => $string_row) {
            if ($key_parent == '0.9.2342.19200300.100.1.25') {
                foreach ($string_row as $string) {
                    $issuer_names[] = 'DC='.$string;
                }
            }
            if ($key_parent == 'CN') {
                $issuer_names[] = 'CN='.$string_row;
            }
        }

        return implode(', ', array_reverse($issuer_names));
    }
}
