<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Factory\ApiClientFactoryInterface;
use PrestaShop\Module\PrestashopFacebook\Http\HttpClient;

class ProductSyncReportProvider
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var HttpClient
     */
    private $psApiClient;

    public function __construct(
        ConfigurationAdapter $configurationAdapter,
        ApiClientFactoryInterface $psApiClientFactory
    ) {
        $this->configurationAdapter = $configurationAdapter;
        $this->psApiClient = $psApiClientFactory->createClient();
    }

    public function getProductSyncReport()
    {
        $businessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);

        $response = $this->psApiClient->get("/account/{$businessId}/reporting");

        $responseContent = $response->getBody();

        if (!$response->isSuccessful()) {
            $responseContent = [];
        }

        return $this->fixMissingValues($responseContent);
    }

    private function fixMissingValues($response)
    {
        if (!isset($response['errors'])) {
            $response['errors'] = [];
        }

        if (!isset($response['lastFinishedSyncStartedAt'])) {
            $response['lastFinishedSyncStartedAt'] = 0;
        }

        $response['errors'] = array_filter($response['errors'], [$this, 'filterErrorsWithoutMessage']);

        return $response;
    }

    /**
     * Hotfix as the Nest API should not return products without message
     *
     * @return bool
     */
    private function filterErrorsWithoutMessage(array $productInError)
    {
        return !empty($productInError);
    }
}
