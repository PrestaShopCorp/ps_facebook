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

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class ConfigurationHandler
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(
        ConfigurationAdapter $configurationAdapter
    ) {
        $this->configurationAdapter = $configurationAdapter;
    }

    public function handle($onboardingInputs)
    {
        $this->saveOnboardingConfiguration($onboardingInputs);
    }

    private function saveOnboardingConfiguration(array $onboardingParams)
    {
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, $onboardingParams['access_token']);
        $this->configurationAdapter->updateValue(Config::PS_PIXEL_ID, isset($onboardingParams['fbe']['pixel_id']) ? $onboardingParams['fbe']['pixel_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PROFILES, isset($onboardingParams['fbe']['profiles']) ? implode(',', $onboardingParams['fbe']['profiles']) : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PAGES, isset($onboardingParams['fbe']['pages']) ? implode(',', $onboardingParams['fbe']['pages']) : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_BUSINESS_MANAGER_ID, isset($onboardingParams['fbe']['business_manager_id']) ? $onboardingParams['fbe']['business_manager_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_AD_ACCOUNT_ID, isset($onboardingParams['fbe']['ad_account_id']) ? $onboardingParams['fbe']['ad_account_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_CATALOG_ID, isset($onboardingParams['fbe']['catalog_id']) ? $onboardingParams['fbe']['catalog_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PIXEL_ENABLED, true);

        $this->configurationAdapter->deleteByName(Config::PS_FACEBOOK_USER_ACCESS_TOKEN_EXPIRATION_DATE);
        $this->configurationAdapter->deleteByName(Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN);
        $this->configurationAdapter->deleteByName(Config::PS_FACEBOOK_CAPI_TEST_EVENT_CODE);
    }

    public function cleanOnboardingConfiguration()
    {
        $dataConfigurationKeys = [
            Config::PS_FACEBOOK_USER_ACCESS_TOKEN,
            Config::PS_FACEBOOK_USER_ACCESS_TOKEN_EXPIRATION_DATE,
            Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN,
            Config::PS_PIXEL_ID,
            Config::PS_FACEBOOK_PROFILES,
            Config::PS_FACEBOOK_PAGES,
            Config::PS_FACEBOOK_BUSINESS_MANAGER_ID,
            Config::PS_FACEBOOK_AD_ACCOUNT_ID,
            Config::PS_FACEBOOK_CATALOG_ID,
            Config::PS_FACEBOOK_PIXEL_ENABLED,
            Config::PS_FACEBOOK_CAPI_TEST_EVENT_CODE,
            Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START,
            Config::PS_FACEBOOK_PRODUCT_SYNC_ON,
            Config::PS_FACEBOOK_FORCED_DISCONNECT,
            Config::PS_FACEBOOK_SUSPENSION_REASON,
        ];

        foreach ($dataConfigurationKeys as $key) {
            $this->configurationAdapter->deleteByName($key);
        }
    }
}
