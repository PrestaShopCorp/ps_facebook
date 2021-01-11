<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Exception;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Config\Env;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookAccountException;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\Ps_facebook\Client\PsApiClient;

class ProductSyncReportProvider
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var Env
     */
    private $env;

    public function __construct(ConfigurationAdapter $configurationAdapter, Env $env)
    {
        $this->configurationAdapter = $configurationAdapter;
        $this->env = $env;
    }

    public function getProductSyncReport()
    {
        $businessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);

        try {
            $client = PsApiClient::create($this->env->get('PSX_FACEBOOK_API_URL'));
            $response = $client->post(
                "/account/{$businessId}/update_product_sync_reporting"
            )->json();
        } catch (Exception $e) {
            $errorHandler = ErrorHandler::getInstance();
            $errorHandler->handle(
                new FacebookAccountException(
                    'Failed to get product sync reporting',
                    FacebookAccountException::FACEBOOK_ACCOUNT_PRODUCT_SYNC_REPORTING_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );

            return false;
        }

        return $response;
    }
}
