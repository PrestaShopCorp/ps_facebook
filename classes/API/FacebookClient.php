<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

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
        $responseContent = $this->get('me', __FUNCTION__, ['email']);

        return new User(
            isset($responseContent['email']) ? $responseContent['email'] : null
        );
    }

    /**
     * @param string $businessManagerId
     *
     * @return FacebookBusinessManager
     */
    public function getBusinessManager($businessManagerId)
    {
        $responseContent = $this->get((int) $businessManagerId, __FUNCTION__, ['name', 'created_time']);

        return new FacebookBusinessManager(
            isset($responseContent['id']) ? $responseContent['id'] : $businessManagerId,
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['created_time']) ? $responseContent['created_time'] : null
        );
    }

    /**
     * @param string $adId
     * @param string $pixelId
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/adspixels/?locale=en_US
     *
     * @return Pixel
     */
    public function getPixel($adId, $pixelId)
    {
        $responseContent = $this->get('act_' . $adId . '/adspixels', __FUNCTION__, ['name', 'last_fired_time', 'is_unavailable']);

        $name = $lastFiredTime = null;
        $isUnavailable = false;

        if (isset($responseContent['data'])) {
            foreach ($responseContent['data'] as $adPixel) {
                if ($adPixel['id'] !== $pixelId) {
                    continue;
                }
                $name = isset($adPixel['name']) ? $adPixel['name'] : null;
                $lastFiredTime = isset($adPixel['last_fired_time']) ? $adPixel['last_fired_time'] : null;
                $isUnavailable = isset($adPixel['is_unavailable']) ? $adPixel['is_unavailable'] : null;
            }
        }

        return new Pixel(
            $pixelId,
            $name,
            $lastFiredTime,
            $isUnavailable,
            (bool) $this->configurationAdapter->get(Config::PS_FACEBOOK_PIXEL_ENABLED)
        );
    }

    /**
     * @param array $pageIds
     *
     * @return Page
     */
    public function getPage(array $pageIds)
    {
        $pageId = reset($pageIds);
        $responseContent = $this->get((int) $pageId, __FUNCTION__, ['name', 'fan_count']);

        $logoResponse = $this->get($pageId . '/photos', __FUNCTION__ . 'Photo', ['picture']);

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

    /**
     * @param string $adId
     *
     * @return Ad
     */
    public function getAd($adId)
    {
        $responseContent = $this->get('act_' . $adId, __FUNCTION__, ['name', 'created_time']);

        return new Ad(
            isset($responseContent['id']) ? $responseContent['id'] : $adId,
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['created_time']) ? $responseContent['created_time'] : null
        );
    }

    public function getFbeAttribute($externalBusinessId)
    {
        $responseContent = $this->get(
            'fbe_business/fbe_installs',
            __FUNCTION__,
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
            __FUNCTION__,
            [],
            [
                'fbe_external_business_id' => $externalBusinessId,
            ]
        );

        if (!is_array($response)) {
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, '');

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
     * @param int $catalogId
     *
     * @return array|false
     */
    public function getProductsInCatalogCount($catalogId)
    {
        $body = [
            'fields' => 'product_count',
        ];

        return $this->post(
            $catalogId,
            [],
            $body
        );
    }

    /**
     * @param int|string $id
     * @param string $method
     * @param array $fields
     * @param array $query
     *
     * @return false|array
     *
     * @throws Exception
     */
    private function get($id, $method, array $fields = [], array $query = [])
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
                    'Facebook client failed when creating get request. Method: ' . $method,
                    FacebookClientException::FACEBOOK_CLIENT_GET_FUNCTION_EXCEPTION,
                    $e
                ),
                $e->getCode(),
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
                $e->getCode(),
                false
            );

            return false;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
