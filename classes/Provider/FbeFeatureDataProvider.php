<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Configuration;
use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class FbeFeatureDataProvider
{
    /**
     * @var FacebookClient
     */
    private $facebookClient;

    public function __construct(FacebookClient $facebookClient)
    {
        $this->facebookClient = $facebookClient;
    }

    public function getFbeFeatures()
    {
        $features = $this->facebookClient->getFbeFeatures(Configuration::get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID));
        $unavailableFeatures = [];

        //TODO: Implement check if products are synchronized
        $productsSynced = false;

        if (empty($features)) {
            return [];
        }

        $features = array_filter($features, function ($key) {
            return in_array($key, Config::AVAILABLE_FBE_FEATURES);
        }, ARRAY_FILTER_USE_KEY);

        $enabledFeatures = array_filter($features, function ($feature) {
            return $feature['enabled'];
        });

        $disabledFeatures = array_filter($features, function ($feature) {
            return !$feature['enabled'];
        });

        if (!$productsSynced) {
            $unavailableFeatures = array_filter($features, function ($key) {
                return in_array($key, Config::FBE_FEATURES_REQUIRING_PRODUCT_SYNC);
            }, ARRAY_FILTER_USE_KEY);

            $disabledFeatures = array_filter($disabledFeatures, function ($key) {
                return !in_array($key, Config::FBE_FEATURES_REQUIRING_PRODUCT_SYNC);
            }, ARRAY_FILTER_USE_KEY);
        }

        return [
            'enabledFeatures' => $enabledFeatures,
            'disabledFeatures' => $disabledFeatures,
            //TODO: make check if products are synced to know if some features can be enabled
            'unavailableFeatures' => $unavailableFeatures,
        ];
    }
}
