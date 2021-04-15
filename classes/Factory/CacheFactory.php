<?php

namespace PrestaShop\Module\PrestashopFacebook\Factory;

use PrestaShop\ModuleLibCacheDirectoryProvider\Cache\CacheDirectoryProvider;

class CacheFactory
{
    /**
     * @return string
     */
    public function getCachePath()
    {
        $cacheDirectoryProvider = new CacheDirectoryProvider(
            _PS_VERSION_,
            _PS_ROOT_DIR_,
            _PS_MODE_DEV_
        );

        return $cacheDirectoryProvider->getPath();
    }
}
