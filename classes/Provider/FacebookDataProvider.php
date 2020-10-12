<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Exception;
use Facebook\Facebook;
use GuzzleHttp\Client;

class FacebookDataProvider
{
    const API_URL = 'https://graph.facebook.com';

    /**
     * @var int
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
     *
     * @param int $appId
     * @param string $sdkVersion
     * @param string $accessToken
     */
    public function __construct($appId, $accessToken, $sdkVersion)
    {
        $this->appId = $appId;
        $this->sdkVersion = $sdkVersion;
        $this->accessToken = $accessToken;
    }

    /**
     * https://github.com/facebookarchive/php-graph-sdk
     * https://developers.facebook.com/docs/graph-api/changelog/version8.0
     *
     * @return array
     */
    public function getContext(array $fbe)
    {
        if (isset($fbe['error'])) {
            return [];
        }

        $apps = [];
        $client = new Client();
        foreach ($fbe as $item) {
            try {
                $response = $client->get(
                    self::API_URL . "/{$this->sdkVersion}/{$item['id']}",
                    [
                        'query' => [
                            'access_token' => $this->accessToken,
                        ],
                    ]
                );
            } catch (Exception $e) {
                $apps[] = [
                    'id' => $item['id'],
                    'statusCode' => $e->getCode(),
                    'errorMessage' => $e->getMessage(),
                ];
                continue;
            }

            $apps[] = json_decode($response->getBody()->getContents(), true);
        }

        return $apps;
    }
}
