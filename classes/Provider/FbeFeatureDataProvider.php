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

        if (empty($features)) {
            return [];
        }

        $features = array_filter($features, function ($key) {
            return in_array($key, Config::AVAILABLE_FBE_FEATURES);
        }, ARRAY_FILTER_USE_KEY);

        $disabledFeatures = array_filter($features, function ($feature) {
            return $feature['enabled'] === false;
        });

        $enabledFeatures = array_filter($features, function ($feature) {
            return $feature['enabled'];
        });

        return [
            'enabledFeatures' => $enabledFeatures,
            'disabledFeatures' => $disabledFeatures,
            'unavailableFeatures' => [],
        ];
    }
}
