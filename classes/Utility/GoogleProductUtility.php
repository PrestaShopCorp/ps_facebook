<?php

namespace PrestaShop\Module\Ps_facebook\Utility;

use PrestaShop\Module\PrestashopFacebook\DTO\GoogleProduct;

class GoogleProductUtility
{
    /**
     * @param string $googleProduct
     *
     * @return GoogleProduct
     */
    public static function googleProductToObject($googleProduct)
    {
        $googleProductSplitted = explode('-', $googleProduct);
        $googleProductObj = new GoogleProduct();
        $googleProductObj->setProductId((int) $googleProductSplitted[GoogleProduct::POSITION_PRODUCT_ID]);
        $googleProductObj->setProductAttributeId((int) $googleProductSplitted[GoogleProduct::POSITION_PRODUCT_ATTRIBUTE_ID]);

        return $googleProductObj;
    }
}
