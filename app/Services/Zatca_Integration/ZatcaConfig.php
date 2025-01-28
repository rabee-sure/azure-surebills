<?php
namespace Allam\Zatca;

use Exception;
use Illuminate\Support\Arr;

/**
 * A class defines zatca required integration defaults
 */
class ZatcaConfig {

    /**
     * Get base url depended on zatca environment
     */
    public static function baseUrl($env)
    {
        return "https://gw-fatoora.zatca.gov.sa/e-invoicing/$env";
    }

    /**
     * Get zatca environment types
     */
    public static function getEnvironments()
    {
        return [
            'developer-portal',
            'simulation',
            'core'
        ];
    }

    /**
     * Get zatca certificate templates
     */
    public static function getCertificateTemplates($env)
    {
        $templates = [
            'developer-portal' => 'TSTZATCA-Code-Signing',
            'simulation' => 'PREZATCA-Code-Signing',
            'core' => 'ZATCA-Code-Signing',
        ];

        return $templates[$env];
    }

    /**
     * Get zatca invoice types into array
     */
    public static function getInvoiceTypes()
    {
        return [
            '1100', // for simplified and standard invoices
            '0100', // for simplified invoices
            '1000', // for standard invoices
        ];
    }
}
