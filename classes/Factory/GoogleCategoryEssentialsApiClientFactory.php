<?php

namespace PrestaShop\Module\PrestashopFacebook\Factory;

use GuzzleHttp\Client;

class GoogleCategoryEssentialsApiClientFactory implements ApiClientFactoryInterface
{
    const API_URL = 'https://facebook-api.psessentials.net';

    public function createClient()
    {
        return new Client(['base_url' => self::API_URL]);
    }
}
