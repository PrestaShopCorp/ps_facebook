<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use FacebookAds\Object\Values\ProductItemAvailabilityValues;
use PrestaShopBundle\Form\Admin\Product\ProductQuantity;
use Product;

class ProductAvailabilityProvider implements ProductAvailabilityProviderInterface
{
    /**
     * todo:add more availability cases
     * @param $productId
     *
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
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
