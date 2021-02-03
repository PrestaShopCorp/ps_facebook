<?php

namespace PrestaShop\Module\Ps_facebook\Utility;

use PrestaShop\Module\PrestashopFacebook\DTO\EventBusProduct;

class EventBusProductUtility
{
    /**
     * @param string $eventBusProduct
     *
     * @return EventBusProduct
     */
    public static function eventBusProductToObject($eventBusProduct)
    {
        $eventBusProductSplitted = explode('-', $eventBusProduct);
        $eventBusProductObj = new EventBusProduct();
        $eventBusProductObj->setProductId((int) $eventBusProductSplitted[EventBusProduct::POSITION_PRODUCT_ID]);
        $eventBusProductObj->setProductAttributeId((int) $eventBusProductSplitted[EventBusProduct::POSITION_PRODUCT_ATTRIBUTE_ID]);

        return $eventBusProductObj;
    }
}
