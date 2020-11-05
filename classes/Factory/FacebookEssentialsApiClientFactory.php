<?php

namespace PrestaShop\Module\PrestashopFacebook\Factory;

use GuzzleHttp\Client;

class FacebookEssentialsApiClientFactory implements ApiClientFactoryInterface
{
    const API_URL = 'https://graph.facebook.com';

    public function createClient()
    {
        return new Client(['base_url' => self::API_URL]);
    }
}
