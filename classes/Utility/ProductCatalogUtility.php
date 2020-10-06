<?php

namespace PrestaShop\Module\Ps_facebook\Utility;

class ProductCatalogUtility
{
    /**
     * @param int $productId
     * @param int|null $productAttributeId
     * @param string $locale
     * @return string
     */
    public static function makeProductId($productId, $productAttributeId, $locale)
    {
        return implode(
            '-',
            [
                (int)$productId,
                (int)$productAttributeId,
                $locale
            ]
        );
    }
}
