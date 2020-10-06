<?php

namespace PrestaShop\Module\Ps_facebook\Utility;

class ProductCatalogUtility
{
    public static function makeProductId($productId, $productAttributeId, $locale)
    {
        return "{$productId}-{$productAttributeId}-{$locale}";
    }
}
