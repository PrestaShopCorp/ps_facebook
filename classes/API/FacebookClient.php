<?php

namespace PrestaShop\Module\PrestashopFacebook\API;

use Exception;
use GuzzleHttp\Client;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\DTO\Ad;
use PrestaShop\Module\PrestashopFacebook\DTO\FacebookBusinessManager;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\user;
use PrestaShop\Module\PrestashopFacebook\DTO\Page;
use PrestaShop\Module\PrestashopFacebook\DTO\Pixel;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Factory\ApiClientFactoryInterface;

class FacebookClient
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $sdkVersion;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param ApiClientFactoryInterface $apiClientFactory
     * @param ConfigurationAdapter $configuration
     */
    public function __construct(ApiClientFactoryInterface $apiClientFactory, ConfigurationAdapter $configuration)
    {
        $this->accessToken = $configuration->get(Config::FB_ACCESS_TOKEN);
        $this->sdkVersion = Config::API_VERSION;
        $this->client = $apiClientFactory->createClient();
    }

    public function getUserEmail()
    {
        $responseContent = $this->get('me', ['email']);

        return new User($responseContent['email']);
    }

    public function getBusinessManager($businessManagerId)
    {
        $responseContent = $this->get((int) $businessManagerId, ['name', 'created_time']);
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
        $responseContent = $this->get((int) $pixelId, ['name', 'last_fired_time', 'is_unavailable']);
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
        $responseContent = $this->get((int) $pageId, ['name', 'fan_count']);
        if (!$responseContent) {
            return null;
        }

        $logoResponse = $this->get($pageId . '/photos', ['picture']);

        $logo = null;
        if (is_array($logoResponse)) {
            $logo = reset($logoResponse['data'])['picture'];
        }

        return new Page(
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['fan_count']) ? $responseContent['fan_count'] : null,
            $logo
        );
    }

    public function getAd($adId)
    {
        $responseContent = $this->get((int) $adId, ['name', 'created_time']);
        if (!$responseContent) {
            return null;
        }

        return new Ad(
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
        $responseContent = $this->get(
            'fbe_business/fbe_installs',
            [],
            [
                'fbe_external_business_id' => $externalBusinessId,
            ]
        );

        return reset($responseContent['data']);
    }

    /**
     * @param int|string $id
     * @param array $fields
     * @param array $query
     *
     * @return false|array
     */
    public function get($id, array $fields = [], array $query = [])
    {
        $query = array_merge(
            [
                'access_token' => $this->accessToken,
                'fields' => implode(',', $fields),
            ],
            $query
        );

        try {
            $request = $this->client->createRequest(
                'GET',
                "/{$this->sdkVersion}/{$id}",
                [
                    'query' => $query,
                ]
            );

            $response = $this->client->send($request);
        } catch (Exception $e) {
            return false;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
