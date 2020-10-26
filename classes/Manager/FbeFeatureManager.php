<?php

namespace PrestaShop\Module\PrestashopFacebook\Manager;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class FbeFeatureManager
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;
    /**
     * @var FacebookClient
     */
    private $facebookClient;

    public function __construct(ConfigurationAdapter $configurationAdapter, FacebookClient $facebookClient)
    {
        $this->configurationAdapter = $configurationAdapter;
        $this->facebookClient = $facebookClient;
    }

    /**
     * @param string $featureName
     * @param bool $state
     * @return false
     */
    public function updateFeature($featureName, $state)
    {
        $featureConfiguration = $this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . $featureName);
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);

        if (!$featureConfiguration) {
            return false;
        }

        $featureConfiguration = json_decode($featureConfiguration);

        if ($featureName == 'messenger_chat') {
            unset($featureConfiguration->default_locale);
        }

        $featureConfiguration->enabled = (bool) $state;
        $configuration = [
            $featureName => $featureConfiguration,
        ];

        return $this->facebookClient->updateFeature($externalBusinessId, json_encode($configuration));
    }
}
