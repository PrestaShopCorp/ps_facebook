<?php

namespace PrestaShop\Module\PrestashopFacebook\Factory;

use GuzzleHttp\Client;

interface ApiClientFactoryInterface
{
    /**
     * @return Client
     */
    public function createClient();
}
