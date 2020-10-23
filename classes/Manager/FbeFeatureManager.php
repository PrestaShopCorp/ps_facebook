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

    public function enableFeature($featureName)
    {
        $featureConfiguration = $this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . $featureName);

        if (!$featureConfiguration) {
            return false;
        }

        $featureConfiguration = json_decode($featureConfiguration);
        $featureConfiguration['enabled'] = true;
        $configuration = [
            $featureName => $featureConfiguration,
        ];

        $this->facebookClient->updateFeature();
    }
}
