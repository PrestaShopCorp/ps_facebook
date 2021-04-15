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
    public function getPrevalidationScanSummary()
    {
        return json_decode($this->preValidationCacheProvider->get(
            PrevalidationScanCacheProvider::CACHE_KEY_SUMMARY
        ));
    }

    /**
     * @param int $page
     *
     * @return array|null
     */
    public function getPageOfPrevalidationScan($page)
    {
        return json_decode($this->preValidationCacheProvider->get(
            PrevalidationScanCacheProvider::CACHE_KEY_PAGE . $page
        ));
    }
}
