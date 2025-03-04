<?php

namespace App\Helpers;

use App\Services\CyberSourceService;

class CybersourceMicroformHandlerHelper
{
    /**
     * Retrieves a Microform session token if the feature is enabled in the configuration.
     *
     * @param string|null $host Optional host URL. Defaults to the request's host if not provided.
     * @return string|null The session token if Microform is enabled, otherwise null.
     */
    public static function retrieveMicroformToken($host = null)
    {
        return config('cybersource.microform_enabled') ? self::getMicroformSessionToken($host) : null;
    }  

    /**
     * Generates a Microform session token from CyberSource.
     *
     * @param string|null $host Optional host URL. If null, it defaults to the request's host.
     * @return string|null The generated session token or null on failure.
     */
    private static function getMicroformSessionToken($host = null)
    {
        $cybersourceService = new CyberSourceService();
        return $cybersourceService->createMicroformSession(self::resolveMicroformHost($host));
    }

    /**
     * Resolves the Microform host URL.
     *
     * @param string|null $host Optional custom host. If provided, it is prefixed with 'https://'.
     * @return string The resolved host URL.
     */
    private static function resolveMicroformHost($host)
    {
        return $host ? 'https://' . $host : request()->getSchemeAndHttpHost();
    }
}
