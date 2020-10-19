<?php

namespace PrestaShop\Module\PrestashopFacebook\API;

use Exception;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Client;
use PrestaShop\Module\PrestashopFacebook\DTO\Ads;
use PrestaShop\Module\PrestashopFacebook\DTO\FacebookBusinessManager;
use PrestaShop\Module\PrestashopFacebook\DTO\Page;
use PrestaShop\Module\PrestashopFacebook\DTO\Pixel;
use PrestaShop\Module\PrestashopFacebook\DTO\user;

class FacebookClient
{
    const API_URL = 'https://graph.facebook.com';

    /**
     * @var int
     */
    private $appId;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $sdkVersion;

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
        $this->accessToken = $accessToken;
        $this->sdkVersion = $sdkVersion;
    }

    public function getUserEmail()
    {
        $responseContent = $this->call('me', ['email']);

        return new User($responseContent['email']);
    }

    public function getBusinessManager($businessManagerId)
    {
        $responseContent = $this->call((int) $businessManagerId, ['name', 'created_time']);
        if (!$responseContent) {
            return null;
        }

        return new FacebookBusinessManager(
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['email']) ? $responseContent['email'] : null,
            isset($responseContent['created_time']) ? $responseContent['created_time'] : null
        );
    }

    public function getPixel($pixelId)
    {
        $responseContent = $this->call((int) $pixelId, ['name', 'last_fired_time', 'is_unavailable']);
        if (!$responseContent) {
            return null;
        }

        return new Pixel(
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['id']) ? $responseContent['id'] : null,
            isset($responseContent['last_fired_time']) ? $responseContent['last_fired_time'] : null,
            isset($responseContent['is_unavailable']) ? !$responseContent['is_unavailable'] : false
        );
    }

    public function getPage($pageIds)
    {
        $pageId = reset($pageIds);
        $responseContent = $this->call((int) $pageId, ['name', 'fan_count']);
        if (!$responseContent) {
            return null;
        }

        $logoResponse = $this->call($pageId . '/photos', ['picture']);
        $logo = reset($logoResponse['data']);

        return new Page(
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['fan_count']) ? $responseContent['fan_count'] : null,
            isset($logo['picture']) ? $logo['picture'] : null
        );
    }

    public function getAds($adsId)
    {
        $responseContent = $this->call((int) $adsId, ['name', 'created_time']);
        if (!$responseContent) {
            return null;
        }

        return new Ads(
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['email']) ? $responseContent['email'] : null,
            isset($responseContent['created_time']) ? $responseContent['created_time'] : null
        );
    }

    public function getCategoriesMatching($catalogId)
    {
        return false;
    }

    public function getFbeAttribute($externalBusinessId)
    {
        $client = new Client();
        /** @var ResponseInterface|null */
        $response = $client->get(
            self::API_URL . '/' . $this->sdkVersion . '/fbe_business/fbe_installs',
            [
                'query' => [
                    'fbe_external_business_id' => $externalBusinessId,
                    'access_token' => $this->accessToken,
                ],
            ]
        );

        if (null === $response || !$response->getBody()) {
            return [];
        }

        $data = json_decode($response->getBody()->getContents(), true);

        return reset($data['data']);
    }

    /**
     * @param int|string $id
     * @param array $fields
     *
     * @return false|array
     */
    public function call($id, array $fields = [])
    {
        $client = new Client();
        try {
            $response = $client->get(
                self::API_URL . "/{$this->sdkVersion}/{$id}",
                [
                    'query' => [
                        'access_token' => $this->accessToken,
                        'fields' => implode(',', $fields),
                    ],
                ]
            );
        } catch (Exception $e) {
            return false;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
