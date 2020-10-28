<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class FbeFeatureDataProvider
{
    /**
     * @var FacebookClient
     */
    private $facebookClient;
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(FacebookClient $facebookClient, ConfigurationAdapter $configurationAdapter)
    {
        $this->facebookClient = $facebookClient;
        $this->configurationAdapter = $configurationAdapter;
    }

    public function getFbeFeatures()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $features = $this->facebookClient->getFbeFeatures($externalBusinessId);
        $unavailableFeatures = [];

        //TODO: Implement check if products are synchronized
        $productsSynced = false;

        $features = array_filter($features, function ($key) {
            return in_array($key, Config::AVAILABLE_FBE_FEATURES);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($features as $featureName => $feature) {
            if ($feature['enabled']) {
                $this->configurationAdapter->updateValue(Config::FBE_FEATURE_CONFIGURATION . $featureName, json_encode($feature));
            }
        }

        $enabledFeatures = array_filter($features, function ($featureName) {
            return $this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . $featureName) !== false;
        }, ARRAY_FILTER_USE_KEY);

        $disabledFeatures = array_filter($features, function ($featureName) {
            return $this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . $featureName) === false;
        }, ARRAY_FILTER_USE_KEY);

//        if (!$productsSynced) {
        $unavailableFeatures = array_filter($features, function ($key) {
            return in_array($key, Config::FBE_FEATURES_REQUIRING_PRODUCT_SYNC);
        }, ARRAY_FILTER_USE_KEY);

        $disabledFeatures = array_filter($disabledFeatures, function ($key) {
            return !in_array($key, Config::FBE_FEATURES_REQUIRING_PRODUCT_SYNC);
        }, ARRAY_FILTER_USE_KEY);
//        }

        return [
            'enabledFeatures' => $enabledFeatures,
            'disabledFeatures' => $disabledFeatures,
            'unavailableFeatures' => $unavailableFeatures,
        ];
    }
}
