<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

interface ProductAvailabilityProviderInterface
{
    /**
     * @param int $productId
     *
     * @return string
     */
    public function getProductAvailability($productId);
}
