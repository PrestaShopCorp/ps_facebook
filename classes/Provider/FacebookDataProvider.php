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

        $businessManager = $this->handleBusinessManager($fbe['business_manager_id']);
        $pixel = $this->handlePixel($fbe['pixel_id']);
        $pages = $this->handlePages($fbe['pages']);
        $ads = $this->handleAds($fbe['business_manager_id']);
        $isCategoriesMatching = $this->handleCategoriesMatching($fbe['catalog_id']);

        return new ContextPsFacebook(
            $businessManager,
            $pixel,
            $pages,
            $ads,
            $isCategoriesMatching
        );
    }

    private function handleBusinessManager($businessManagerId)
    {
        $responseContent = $this->handleAPICall((int) $businessManagerId);
        if (!$responseContent) {
            return null;
        }

        return new FacebookBusinessManager(
            isset($responseContent['name']) ? $responseContent['name'] : '',
            isset($responseContent['email']) ? $responseContent['email'] : '',
            isset($responseContent['createdAt']) ? $responseContent['createdAt'] : 0
        );
    }

    private function handlePixel($pixelId)
    {
        $responseContent = $this->handleAPICall((int) $pixelId);
        if (!$responseContent) {
            return null;
        }

        return new Pixel(
            isset($responseContent['name']) ? $responseContent['name'] : '',
            isset($responseContent['id']) ? $responseContent['id'] : '',
            isset($responseContent['lastActive']) ? $responseContent['lastActive'] : 0,
            isset($responseContent['activated']) ? $responseContent['activated'] : 0
        );
    }

    private function handlePages($pageIds)
    {
        $pages = [];
        foreach ($pageIds as $pageId) {
            $responseContent = $this->handleAPICall((int) $pageId);
            if (!$responseContent) {
                return null;
            }

            $page = new Page(
                $responseContent['page'],
                $responseContent['likes'],
                $responseContent['logo']
            );
            $pages[] = $page;
        }

        return $pages;
    }

    private function handleAds($adsId)
    {
        $responseContent = $this->handleAPICall((int) $adsId);
        if (!$responseContent) {
            return null;
        }

        return new Ads(
            isset($responseContent['name']) ? $responseContent['name'] : '',
            isset($responseContent['email']) ? $responseContent['email'] : '',
            isset($responseContent['createdAt']) ? $responseContent['createdAt'] : 0
        );
    }

    private function handleCategoriesMatching($catalogId)
    {
        return false;
    }

    /**
     * @param int $id
     *
     * @return false|array
     */
    private function handleAPICall($id)
    {
        $client = new Client();
        try {
            $response = $client->get(
                self::API_URL . "/{$this->sdkVersion}/{$id}",
                [
                    'query' => [
                        'access_token' => $this->accessToken,
                    ],
                ]
            );
        } catch (Exception $e) {
            return false;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
