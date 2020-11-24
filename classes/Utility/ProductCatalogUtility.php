<?php

namespace PrestaShop\Module\Ps_facebook\Utility;

class ProductCatalogUtility
{
    /**
     * @param int $productId
     * @param int|null $productAttributeId
     *
     * @return string
     */
    public static function makeProductId($productId, $productAttributeId)
    {
        return implode(
            '-',
            [
                (int) $productId,
                (int) $productAttributeId,
            ]
        );
    }
}
