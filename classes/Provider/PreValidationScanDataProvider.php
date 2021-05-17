<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

class PrevalidationScanDataProvider
{
    /**
     * @var PrevalidationScanCacheProvider
     */
    protected $preValidationCacheProvider;

    public function __construct(PrevalidationScanCacheProvider $preValidationCacheProvider)
    {
        $this->preValidationCacheProvider = $preValidationCacheProvider;
    }

    /**
     * @return array|null
     */
    public function getPrevalidationScanSummary($shopId)
    {
        return json_decode($this->preValidationCacheProvider->get(
            PrevalidationScanCacheProvider::CACHE_KEY_SUMMARY . $shopId
        ));
    }

    /**
     * @param int $page
     * @param int $shopId
     *
     * @return array|null
     */
    public function getPageOfPrevalidationScan($shopId, $page)
    {
        return json_decode($this->preValidationCacheProvider->get(
            PrevalidationScanCacheProvider::CACHE_KEY_PAGE . $shopId . '_' . $page
        ));
    }
}
