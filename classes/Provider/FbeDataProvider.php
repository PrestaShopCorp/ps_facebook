<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class FbeDataProvider
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(ConfigurationAdapter $configurationAdapter)
    {
        $this->configurationAdapter = $configurationAdapter;
    }

    /**
     * @return array
     */
    public function getFbeData()
    {
        return [
            'pixel_id' => $this->configurationAdapter->get(Config::PS_PIXEL_ID),
            'profiles' => $this->configurationAdapter->get(Config::PS_FACEBOOK_PROFILES),
            'pages' => [
                $this->configurationAdapter->get(Config::PS_FACEBOOK_PAGES),
            ],
            'business_manager_id' => $this->configurationAdapter->get(Config::PS_FACEBOOK_BUSINESS_MANAGER_ID),
            'catalog_id' => $this->configurationAdapter->get(Config::PS_FACEBOOK_CATALOG_ID),
            'ad_account_id' => $this->configurationAdapter->get(Config::PS_FACEBOOK_AD_ACCOUNT_ID),
        ];
    }
}
