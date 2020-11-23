<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\DTO\Catalog;
use PrestaShop\Module\PrestashopFacebook\DTO\ContextPsFacebook;

class FacebookDataProvider
{
    /**
     * @var FacebookClient
     */
    protected $facebookClient;

    public function __construct(FacebookClient $facebookClient)
    {
        $this->facebookClient = $facebookClient;
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
        if (isset($fbe['error']) || !$this->facebookClient->hasAccessToken()) {
            return null;
        }

        $user = $this->facebookClient->getUserEmail();
        $businessManager = $this->facebookClient->getBusinessManager($fbe['business_manager_id']);
        $pixel = $this->facebookClient->getPixel($fbe['pixel_id']);
        $pages = $this->facebookClient->getPage($fbe['pages']);
        $ad = $this->facebookClient->getAd($fbe['ad_account_id']);
        $catalog = new Catalog($fbe['catalog_id']); // No additional data retrieved from FB
        $isCategoriesMatching = $this->facebookClient->getCategoriesMatching($fbe['catalog_id']);

        return new ContextPsFacebook(
            $user,
            $businessManager,
            $pixel,
            $pages,
            $ad,
            $catalog,
            $isCategoriesMatching
        );
    }
}
