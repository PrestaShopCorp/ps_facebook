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
        if (!$this->facebookClient->hasAccessToken()) {
            return false;
        }
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $features = $this->facebookClient->getFbeFeatures($externalBusinessId);
        $unavailableFeatures = [];

        $productsSynced = $this->configurationAdapter->get(Config::PS_FACEBOOK_PRODUCT_SYNC_ON);

        $features = array_filter($features, function ($key) {
            return in_array($key, Config::AVAILABLE_FBE_FEATURES);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($features as $featureName => $feature) {
            if ($feature['enabled'] || $this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . $featureName) !== false) {
                $this->configurationAdapter->updateValue(Config::FBE_FEATURE_CONFIGURATION . $featureName, json_encode($feature));
            }
        }

        $enabledFeatures = array_filter($features, function ($featureName) {
            return $this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . $featureName) !== false;
        }, ARRAY_FILTER_USE_KEY);

        $disabledFeatures = array_filter($features, function ($featureName) {
            return $this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . $featureName) === false;
        }, ARRAY_FILTER_USE_KEY);

        if (!$productsSynced) {
            $unavailableFeatures = array_filter($features, function ($key) use ($enabledFeatures) {
                return in_array($key, Config::FBE_FEATURES_REQUIRING_PRODUCT_SYNC)
                    && in_array($key, $enabledFeatures);
            }, ARRAY_FILTER_USE_KEY);
        }

        return [
            'enabledFeatures' => $enabledFeatures,
            'disabledFeatures' => $disabledFeatures,
            'unavailableFeatures' => $unavailableFeatures,
        ];
    }
}
