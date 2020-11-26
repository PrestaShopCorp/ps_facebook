<?php

namespace PrestaShop\Module\PrestashopFacebook\API;

use Exception;
use GuzzleHttp\Client;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\Ad;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\FacebookBusinessManager;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\Page;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\Pixel;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\user;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookClientException;
use PrestaShop\Module\PrestashopFacebook\Factory\ApiClientFactoryInterface;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
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
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    /**
     * @param ApiClientFactoryInterface $apiClientFactory
     * @param AccessTokenProvider $accessTokenProvider
     * @param ConfigurationAdapter $configurationAdapter
     * @param ErrorHandler $errorHandler
     */
    public function __construct(
        ApiClientFactoryInterface $apiClientFactory,
        AccessTokenProvider $accessTokenProvider,
        ConfigurationAdapter $configurationAdapter,
        ErrorHandler $errorHandler
    ) {
        $this->accessToken = $accessTokenProvider->getUserAccessToken();
        $this->sdkVersion = Config::API_VERSION;
        $this->client = $apiClientFactory->createClient();
        $this->configurationAdapter = $configurationAdapter;
        $this->errorHandler = $errorHandler;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return bool
     */
    public function hasAccessToken()
    {
        return (bool) $this->accessToken;
    }

    public function getUserEmail()
    {
        $responseContent = $this->get('me', ['email']);

        return new User(
            isset($responseContent['email']) ? $responseContent['email'] : null
        );
    }

    public function getBusinessManager($businessManagerId)
    {
        $responseContent = $this->get((int) $businessManagerId, ['name', 'created_time']);

        return new FacebookBusinessManager(
            isset($responseContent['id']) ? $responseContent['id'] : $businessManagerId,
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['email']) ? $responseContent['email'] : null,
            isset($responseContent['created_time']) ? $responseContent['created_time'] : null
        );
    }

    public function getPixel($pixelId)
    {
        $responseContent = $this->get((int) $pixelId, ['name', 'last_fired_time', 'is_unavailable']);

        return new Pixel(
            isset($responseContent['id']) ? $responseContent['id'] : $pixelId,
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['last_fired_time']) ? $responseContent['last_fired_time'] : null,
            isset($responseContent['is_unavailable']) ? !$responseContent['is_unavailable'] : false,
            (bool) $this->configurationAdapter->get(Config::PS_FACEBOOK_PIXEL_ENABLED)
        );
    }

    public function getPage($pageIds)
    {
        $pageId = reset($pageIds);
        $responseContent = $this->get((int) $pageId, ['name', 'fan_count']);

        $logoResponse = $this->get($pageId . '/photos', ['picture']);

        $logo = null;
        if (is_array($logoResponse)) {
            $logo = reset($logoResponse['data'])['picture'];
        }

        return new Page(
            isset($responseContent['id']) ? $responseContent['id'] : $pageIds,
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['fan_count']) ? $responseContent['fan_count'] : null,
            $logo
        );
    }

    public function getAd($adId)
    {
        $responseContent = $this->get('act_' . $adId, ['name', 'created_time']);

        return new Ad(
            isset($responseContent['id']) ? $responseContent['id'] : $adId,
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['created_time']) ? $responseContent['created_time'] : null
        );
    }

    // todo: finish categories matching
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
            $this->errorHandler->handle(
                new FacebookClientException(
                    'Facebook client failed when creating get request.',
                    FacebookClientException::FACEBOOK_CLIENT_GET_FUNCTION_EXCEPTION,
                    $e
                ),
                FacebookClientException::FACEBOOK_CLIENT_GET_FUNCTION_EXCEPTION,
                false
            );

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
            $this->errorHandler->handle(
                new FacebookClientException(
                    'Facebook client failed when creating post request.',
                    FacebookClientException::FACEBOOK_CLIENT_POST_FUNCTION_EXCEPTION,
                    $e
                ),
                FacebookClientException::FACEBOOK_CLIENT_POST_FUNCTION_EXCEPTION,
                false
            );

            return false;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
