<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Facebook\Facebook;
use GuzzleHttp\Client;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Exception;

class FacebookDataProvider
{
    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $sdkVersion;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * FacebookDataProvider constructor.
     * @param string $appId
     * @param string $sdkVersion
     * @param string $accessToken
     */
    public function __construct($appId, $sdkVersion, $accessToken)
    {
        $this->appId = $appId;
        $this->sdkVersion = $sdkVersion;
        $this->accessToken = $accessToken;
    }

    /**
     * https://github.com/facebookarchive/php-graph-sdk
     *
     * @return string
     */
    public function getContext()
    {
        $client = new Client();
        $response = $client->get(
            "https://graph.facebook.com/{$this->sdkVersion}/{$this->appId}",
            [
                'headers' =>

                    [
                        'access_token' => $this->accessToken
                    ]
            ]
        );

        if (!$response || !$response->getBody()) {
            return false;
        }


        return $response;
    }
}
