<?php

namespace PrestaShop\Module\PrestashopFacebook\API;

use Exception;
use GuzzleHttp\Client;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\DTO\Ad;
use PrestaShop\Module\PrestashopFacebook\DTO\FacebookBusinessManager;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\user;
use PrestaShop\Module\PrestashopFacebook\DTO\Page;
use PrestaShop\Module\PrestashopFacebook\DTO\Pixel;
use PrestaShop\Module\PrestashopFacebook\Factory\ApiClientFactoryInterface;
use PrestaShop\Module\PrestashopFacebook\Provider\AccessTokenProvider;

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
     * @var AccessTokenProvider
     */
    private $accessTokenProvider;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @param ApiClientFactoryInterface $apiClientFactory
     * @param AccessTokenProvider $accessTokenProvider
     * @param ConfigurationAdapter $configurationAdapter
     */
    public function __construct(ApiClientFactoryInterface $apiClientFactory, AccessTokenProvider $accessTokenProvider, ConfigurationAdapter $configurationAdapter)
    {
        $this->accessToken = $accessTokenProvider->getOrRefreshToken();
        $this->sdkVersion = Config::API_VERSION;
        $this->client = $apiClientFactory->createClient();
        $this->configurationAdapter = $configurationAdapter;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getUserEmail()
    {
        $responseContent = $this->get('me', ['email']);

        if (!$responseContent) {
            return null;
        }

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
            isset($responseContent['id']) ? $responseContent['id'] : $pixelId,
            isset($responseContent['last_fired_time']) ? $responseContent['last_fired_time'] : null,
            isset($responseContent['is_unavailable']) ? !$responseContent['is_unavailable'] : false,
            (bool) $this->configurationAdapter->get(Config::PS_FACEBOOK_PIXEL_ENABLED)
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

    public function getFbeFeatures($externalBusinessId)
    {
        $response = $this->get(
            'fbe_business',
            [],
            [
                'fbe_external_business_id' => $externalBusinessId,
            ]
        );

        if (!is_array($response)) {
            return [];
        }

        return $response;
    }

    public function updateFeature($externalBusinessId, $configuration)
    {
        $body = [
            'fbe_external_business_id' => $externalBusinessId,
            'business_config' => $configuration,
        ];

        return $this->post(
            'fbe_business',
            [],
            $body
        );
    }

    /**
     * @see https://developers.facebook.com/docs/marketing-api/fbe/fbe2/guides/uninstall?locale=en_US#uninstall-fbe--v2-for-businesses
     *
     * @param string $externalBusinessId
     * @param string $accessToken
     *
     * @return false|array
     */
    public function uninstallFbe($externalBusinessId, $accessToken)
    {
        $body = [
            'fbe_external_business_id' => $externalBusinessId,
            'access_token' => $accessToken,
        ];

        return $this->delete(
            'fbe_business/fbe_installs',
            [],
            $body
        );
    }

    /**
     * @param int|string $id
     * @param array $fields
     * @param array $query
     *
     * @return false|array
     */
    private function get($id, array $fields = [], array $query = [])
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

    /**
     * @param int|string $id
     * @param array $headers
     * @param array $body
     *
     * @return false|array
     */
    private function post($id, array $headers = [], array $body = [])
    {
        return $this->sendRequest($id, $headers, $body, 'POST');
    }

    /**
     * @param int|string $id
     * @param array $headers
     * @param array $body
     *
     * @return false|array
     */
    private function delete($id, array $headers = [], array $body = [])
    {
        return $this->sendRequest($id, $headers, $body, 'DELETE');
    }

    /**
     * @param int|string $id
     * @param array $headers
     * @param array $body
     * @param string $method
     *
     * @return false|array
     */
    private function sendRequest($id, array $headers, array $body, $method)
    {
        $options = [
            'headers' => $headers,
            'body' => array_merge(
                [
                    'access_token' => $this->accessToken,
                ],
                $body
            ),
        ];

        try {
            $request = $this->client->createRequest(
                $method,
                "/{$this->sdkVersion}/{$id}",
                $options
            );

            $response = $this->client->send($request);
        } catch (Exception $e) {
            return false;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
