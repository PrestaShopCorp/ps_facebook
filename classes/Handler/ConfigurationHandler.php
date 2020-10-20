<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use GuzzleHttp\Client;
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

    /**
     * @var FacebookDataProvider
     */
    private $facebookDataProvider;

    public function __construct(
        ConfigurationAdapter $configurationAdapter,
        FacebookDataProvider $facebookDataProvider
    ) {
        $this->configurationAdapter = $configurationAdapter;
        $this->facebookDataProvider = $facebookDataProvider;
    }

    public function handle($onboardingInputs)
    {
        $this->addFbeAttributeIfMissing($onboardingInputs);
        $this->saveOnboardingConfiguration($onboardingInputs);

        $facebookContext = $this->facebookDataProvider->getContext($onboardingInputs['fbe']);

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
            $onboardingParams['access_token'],
            Config::API_VERSION,
            new Client()
        );

        $onboardingParams['fbe'] = $facebookClient->getFbeAttribute($this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID));
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
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PIXEL_ENABLED, true);
    }
}
