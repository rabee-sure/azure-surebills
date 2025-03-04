<?php

namespace App\Services;

use CyberSource\Api\ManageWebhooksApi;
use Illuminate\Support\Facades\Log;
use CyberSource\Authentication\Core\MerchantConfiguration;
use CyberSource\Configuration;
use CyberSource\ApiClient;
use CyberSource\Api\CreateNewWebhooksApi;
use CyberSource\Model\CreateWebhookRequest;
use CyberSource\Model\Kmsegressv2keyssymKeyInformation;
use CyberSource\Model\SaveSymEgressKey;
use Exception;

class CyberSourceWebhookService
{
    private $apiUrl;
    private $apiClient;
    private $webhooksApi;
    private $webhooksManagmentApi;

    public function __construct()
    {
        $this->apiUrl = config('cybersource.api_url.' . config('cybersource.environment'));

        $config = new Configuration();
        $merchantConfig = new MerchantConfiguration();
        $merchantConfig->setMerchantId(config('cybersource.merchant_id'));
        $merchantConfig->setAuthenticationType('HTTP_SIGNATURE');
        $merchantConfig->setRunEnvironment(config('cybersource.environment'));
        $merchantConfig->setSecretKey(config('cybersource.secret_key'));
        $merchantConfig->setApiKeyID(config('cybersource.api_key'));
        $merchantConfig->setIntermediateHost($this->apiUrl);
        $this->apiClient = new ApiClient($config, $merchantConfig);
        $this->webhooksApi = new CreateNewWebhooksApi($this->apiClient);
        $this->webhooksManagmentApi = new ManageWebhooksApi($this->apiClient);
    }

    /* Find Products You Can Subscribe To
    * Documentation: https://developer.cybersource.com/content/dam/docs/cybs/en-us/webhooks/implementation/all/rest/webhooks.pdf (Page: 18)
    * Library: https://github.com/CyberSource/cybersource-rest-client-php/blob/master/docs/Api/CreateNewWebhooksApi.md#findproductstosubscribe
    */
    public function findProductsToSubscribe()
    {
        try {
            $result = $this->webhooksApi->findProductsToSubscribe(config('cybersource.merchant_id'));
            $this->logResult('cybersource_find_products_to_subscribe_result', $result);
            return $result;
        } catch (Exception $e) {
            $this->logResult('cybersource_find_products_to_subscribe_faild', $e->getMessage());
            return false;
        }
    }

    /* Create webhook subscription using mutual trust
    * Documentation: https://developer.cybersource.com/content/dam/docs/cybs/en-us/webhooks/implementation/all/rest/webhooks.pdf (Page: 19)
    * Library: https://github.com/CyberSource/cybersource-rest-client-php/blob/master/docs/Api/CreateNewWebhooksApi.md#createwebhooksubscription
    */
    public function createWebhookSubscription($productId, $eventTypes)
    {
        $subscribtionRequest = [
            "name" => "SureBills webhook subscription for product " . $productId,
            "description" => "webhook subscription for surebill for product " . $productId,
            "organizationId" => config('cybersource.merchant_id'),
            "productId" => $productId,
            "eventTypes" => $eventTypes,
            "webhookUrl" => config('cybersource.webhook_url'),
            "healthCheckUrl" => config('cybersource.webhook_health_check_url'),
            "notificationScope" => "SELF",
            "securityPolicy" => [
                "securityType" => "KEY",
                "proxyType" => "external"
            ],
            "retryPolicy" => [
                "algorithm" => "ARITHMETIC",
                "firstRetry" => "1",
                "interval" => "1",
                "numberOfRetries" => "100",
                "deactivateFlag" => "false",
                "repeatSequenceCount" => "0",
                "repeatSequenceWaitTime" => "0"
            ]
        ];

        try {
            $createWebhookSubscriptionObject = new CreateWebhookRequest($subscribtionRequest);
            $result = $this->webhooksApi->createWebhookSubscription($createWebhookSubscriptionObject);
            $this->logResult('cybersource_create_webhook_to_subscription_result', $result);
            return $result;
        } catch (Exception $e) {
            $this->logResult('cybersource_create_webhook_to_subscription_faild', json_encode($subscribtionRequest));
            return false;
        }
    }

    /* Create Webhook Security Keys
    * Documentation: https://developer.cybersource.com/content/dam/docs/cybs/en-us/webhooks/implementation/all/rest/webhooks.pdf (Page: 16)
    * Library: https://github.com/CyberSource/cybersource-rest-client-php/blob/master/docs/Api/CreateNewWebhooksApi.md#saveSymEgressKey
    */
    public function createWebhookSecurityKeys()
    {
        try {
            $vCSenderOrganizationId = config('cybersource.merchant_id');
            $vCPermissions = "payment.authorized";
            $vCCorrelationId = null;
            $requestingDigitalSignatureKeyObject = new SaveSymEgressKey([
                'clientReferenceInformation' => null,
                'clientRequestAction' => 'CREATE',
                'keyInformation' => new Kmsegressv2keyssymKeyInformation([
                    'provider' => "nrtd",
                    'tenant' => config('cybersource.merchant_id'),
                    'keyType' => 'sharedSecret',
                    'organizationId' => config('cybersource.merchant_id'),
                    'clientKeyId' => null,
                    'keyId' => null,
                    'key' => null,
                    'status' => null,
                    'expiryDuration' => null,
                ]),
            ]);

            $result = $this->webhooksApi->saveSymEgressKey($vCSenderOrganizationId, $vCPermissions, $vCCorrelationId, $requestingDigitalSignatureKeyObject);
            $this->logResult('cybersource_create_webhook_security_keys_result', $result);
            return $result;
        } catch (Exception $e) {
            $this->logResult('cybersource_create_webhook_security_keys_faild', $e->getMessage());
            return false;
        }
    }

    /* Get Webhook subscription details
    * Library: https://github.com/CyberSource/cybersource-rest-client-php/blob/master/docs/Api/ManageWebhooksApi.md#deleteWebhookSubscription
    */
    public function getWebhookSubscriptionById($webhookId)
    {
        try {
            $result = $this->webhooksManagmentApi->getWebhookSubscriptionById($webhookId);
            $this->logResult('cybersource_get_webhook_subscription_result', $result);
            return $result;
        } catch (Exception $e) {
            $this->logResult('cybersource_get_webhook_subscription_faild', $e->getMessage());
            return false;
        }
    }

    public function getWebhookSubscriptionsByOrg($organizationId, $productId, $eventType = null)
    {
        try {
            $result = $this->webhooksManagmentApi->getWebhookSubscriptionsByOrg($organizationId, $productId, $eventType);
            $this->logResult('cybersource_get_webhook_subscription_by_organization_result', $result);
            return $result;
        } catch (Exception $e) {
            $this->logResult('cybersource_get_webhook_subscription_by_organization_faild', $e->getMessage());
            return false;
        }
    }

    /* Delete Webhook subscription
    * Library: https://github.com/CyberSource/cybersource-rest-client-php/blob/master/docs/Api/ManageWebhooksApi.md#deleteWebhookSubscription
    */
    public function deleteWebhookSubscription($webhookId)
    {
        try {
            $result = $this->webhooksManagmentApi->deleteWebhookSubscription($webhookId);
            $this->logResult('cybersource_delete_webhook_subscription_result', $result);
            return $result;
        } catch (Exception $e) {
            $this->logResult('delete_webhook_subscription_faild', $e->getMessage());
            return false;
        }
    }

    /**
     * Logs the result of an operation to a file.
     *
     * @param string $fileName The name of the log file.
     * @param mixed $result The result to be logged.
     */
    private function logResult($fileName, $result)
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/' . $fileName . '.log'), 'level' => 'debug'])->error($result);
    }
}
