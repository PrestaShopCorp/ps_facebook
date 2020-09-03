<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\DTO\Object\FacebookProduct;

interface CatalogProvider
{
    /**
     * @return FacebookProduct[]|array
     */
    public function getProducts();
}
