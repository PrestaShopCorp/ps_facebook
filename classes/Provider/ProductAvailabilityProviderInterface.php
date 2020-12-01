<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

interface ProductAvailabilityProviderInterface
{
    /**
     * @param $productId
     *
     * @return string
     */
    public function getProductAvailability($productId);
}
