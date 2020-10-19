<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;

class ConfigurationHandler
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(ConfigurationAdapter $configurationAdapter)
    {
        $this->configurationAdapter = $configurationAdapter;
    }

    public function handle($onboardingInputs)
    {
        $this->addFbeAttributeIfMissing($onboardingInputs);
        $this->saveOnboardingConfiguration($onboardingInputs);

        $facebookClient = new FacebookClient(
            Config::APP_ID,
            $this->configurationAdapter->get(Config::FB_ACCESS_TOKEN),
            Config::API_VERSION
        );
        $fbDataProvider = new FacebookDataProvider($facebookClient);

        $facebookContext = $fbDataProvider->getContext($onboardingInputs['fbe']);

        return [
            'success' => true,
            'contextPsFacebook' => $facebookContext,
        ];
    }

    private function addFbeAttributeIfMissing(array &$onboardingParams)
    {
        if (!empty($onboardingParams['fbe']) && !isset($onboardingParams['fbe']['error'])) {
            return;
        }

        $facebookClient = new FacebookClient(
            Config::APP_ID,
            $onboardingParams['access_token'],
            Config::API_VERSION
        );

        $onboardingParams['fbe'] = $facebookClient->getFbeAttribute($this->configurationAdapter->get('PS_FACEBOOK_EXTERNAL_BUSINESS_ID'));
    }

    private function saveOnboardingConfiguration(array $onboardingParams)
    {
        $this->configurationAdapter->updateValue(Config::FB_ACCESS_TOKEN, $onboardingParams['access_token']);
        $this->configurationAdapter->updateValue(Config::PS_PIXEL_ID, isset($onboardingParams['fbe']['pixel_id']) ? $onboardingParams['fbe']['pixel_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PROFILES, isset($onboardingParams['fbe']['profiles']) ? implode(',', $onboardingParams['fbe']['profiles']) : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PAGES, isset($onboardingParams['fbe']['pages']) ? implode(',', $onboardingParams['fbe']['pages']) : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_BUSINESS_MANAGER_ID, isset($onboardingParams['fbe']['business_manager_id']) ? $onboardingParams['fbe']['business_manager_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_AD_ACCOUNT_ID, isset($onboardingParams['fbe']['ad_account_id']) ? $onboardingParams['fbe']['ad_account_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_CATALOG_ID, isset($onboardingParams['fbe']['catalog_id']) ? $onboardingParams['fbe']['catalog_id'] : '');
    }
}
