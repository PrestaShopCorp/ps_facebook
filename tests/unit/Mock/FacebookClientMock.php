<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock;

use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;

class FacebookClientMock extends FacebookClient
{
    private $fbeFeatures;

    public function __construct()
    {
        $this->fbeFeatures = [
            'business' => [
                'name' => 'PrestaShop',
            ],
            'catalog_feed_scheduled' => [
                'enabled' => false,
            ],
            'ig_cta' => [
                'enabled' => false,
            ],
            'ig_shopping' => [
                'enabled' => false,
            ],
            'messenger_chat' => [
                'enabled' => false,
            ],
            'messenger_menu' => [
                'enabled' => false,
            ],
            'page_card' => [
                'enabled' => false,
            ],
            'page_cta' => [
                'enabled' => false,
            ],
            'page_post' => [
                'enabled' => false,
            ],
            'page_shop' => [
                'enabled' => false,
            ],
            'thread_intent' => [
                'enabled' => false,
            ],
        ];
    }

    public function switchFeature($feature, $active)
    {
        $this->fbeFeatures[$feature]['enabled'] = $active;

        return $this;
    }

    public function getFbeFeatures($externalBusinessId)
    {
        return $this->fbeFeatures;
    }

    public function hasAccessToken()
    {
        return true;
    }
}
