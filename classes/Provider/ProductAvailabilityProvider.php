<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use FacebookAds\Object\Values\ProductItemAvailabilityValues;
use Product;

class ProductAvailabilityProvider implements ProductAvailabilityProviderInterface
{
    /**
     * todo:add more availability cases
     *
     * @param int $productId
     *
     * @return string
     */
    public function getProductAvailability($productId)
    {
        $productQuantity = Product::getQuantity($productId);
        if ($productQuantity > 0) {
            return ProductItemAvailabilityValues::IN_STOCK;
        }

        return ProductItemAvailabilityValues::OUT_OF_STOCK;
    }
}
