<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Exception;
use GuzzleHttp\Client;
use PrestaShop\Module\PrestashopFacebook\DTO\Ads;
use PrestaShop\Module\PrestashopFacebook\DTO\ContextPsFacebook;
use PrestaShop\Module\PrestashopFacebook\DTO\FacebookBusinessManager;
use PrestaShop\Module\PrestashopFacebook\DTO\Page;
use PrestaShop\Module\PrestashopFacebook\DTO\Pixel;

class FacebookDataProvider
{
    const API_URL = 'https://graph.facebook.com';

    /**
     * @var int
     */
    protected $appId;

    /**
     * @var string
     */
    protected $sdkVersion;

    /**
     * @var string
     */
    protected $accessToken;

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
     **
     * @param array $fbe
     *
     * @return ContextPsFacebook|null
     */
    public function getContext(array $fbe)
    {
        if (isset($fbe['error'])) {
            return null;
        }

        $email = $this->handleUserInformation();
        $businessManager = $this->handleBusinessManager($fbe['business_manager_id']);
        $pixel = $this->handlePixel($fbe['pixel_id']);
        $pages = $this->handlePages($fbe['pages']);
        $ads = $this->handleAds($fbe['business_manager_id']);
        $isCategoriesMatching = $this->handleCategoriesMatching($fbe['catalog_id']);

        return new ContextPsFacebook(
            $email,
            $businessManager,
            $pixel,
            $pages,
            $ads,
            $isCategoriesMatching
        );
    }

    public function handleUserInformation()
    {
        $responseContent = $this->handleAPICall('me', ['email']);

        return $responseContent['email'];
    }

    private function handleBusinessManager($businessManagerId)
    {
        $responseContent = $this->handleAPICall((int) $businessManagerId, ['name', 'created_time']);
        if (!$responseContent) {
            return null;
        }

        return new FacebookBusinessManager(
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['email']) ? $responseContent['email'] : null,
            isset($responseContent['created_time']) ? $responseContent['created_time'] : null
        );
    }

    private function handlePixel($pixelId)
    {
        $responseContent = $this->handleAPICall((int) $pixelId, ['name', 'last_fired_time', 'is_unavailable']);
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

    private function handlePages($pageIds)
    {
        $pages = [];
        foreach ($pageIds as $pageId) {
            $responseContent = $this->handleAPICall((int) $pageId, ['name', 'fan_count']);
            if (!$responseContent) {
                return null;
            }

            $logoResponse = $this->handleAPICall($pageId . '/photos', ['picture']);
            $logo = reset($logoResponse['data']);
            $page = new Page(
                isset($responseContent['name']) ? $responseContent['name'] : null,
                isset($responseContent['fan_count']) ? $responseContent['fan_count'] : null,
                isset($logo['picture']) ? $logo['picture'] : null
            );
            $pages[] = $page;
        }

        return $pages;
    }

    private function handleAds($adsId)
    {
        $responseContent = $this->handleAPICall((int) $adsId, ['name', 'created_time']);
        if (!$responseContent) {
            return null;
        }

        return new Ads(
            isset($responseContent['name']) ? $responseContent['name'] : null,
            isset($responseContent['email']) ? $responseContent['email'] : null,
            isset($responseContent['created_time']) ? $responseContent['created_time'] : null
        );
    }

    private function handleCategoriesMatching($catalogId)
    {
        return false;
    }

    /**
     * @param int|string $id
     * @param array $fields
     *
     * @return false|array
     */
    private function handleAPICall($id, array $fields = [])
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
