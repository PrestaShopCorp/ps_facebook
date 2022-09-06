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

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\Client\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\DTO\ContextPsFacebook;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\Catalog;

class FacebookDataProvider
{
    /**
     * @var FacebookClient
     */
    protected $facebookClient;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(FacebookClient $facebookClient, ConfigurationAdapter $configurationAdapter)
    {
        $this->facebookClient = $facebookClient;
        $this->configurationAdapter = $configurationAdapter;
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
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $hasFbeFeatures = (bool) $this->facebookClient->getFbeFeatures($externalBusinessId);
        if (!$hasFbeFeatures) {
            return null;
        }

        $user = $this->facebookClient->getUserEmail();
        $businessManager = $this->facebookClient->getBusinessManager($fbe['business_manager_id']);
        $pixel = $this->facebookClient->getPixel($fbe['ad_account_id'], $fbe['pixel_id']);
        $pages = $this->facebookClient->getPage($fbe['pages']);
        $ad = $this->facebookClient->getAd($fbe['ad_account_id']);

        $productSyncStarted = (bool) $this->configurationAdapter->get(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START);
        $categoryMatchingStarted = false; // TODO : must be true only if all parent categories are matched !
        $catalog = new Catalog($fbe['catalog_id'], $productSyncStarted, $categoryMatchingStarted);

        return new ContextPsFacebook(
            $user,
            $businessManager,
            $pixel,
            $pages,
            $ad,
            $catalog
        );
    }

    public function getProductsInCatalogCount()
    {
        $catalogId = $this->configurationAdapter->get(Config::PS_FACEBOOK_CATALOG_ID);

        return $this->facebookClient->getProductsInCatalogCount($catalogId);
    }
}
