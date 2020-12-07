<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use FacebookAds\Object\Values\ProductItemAvailabilityValues;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use Product;

class ProductAvailabilityProvider implements ProductAvailabilityProviderInterface
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(ConfigurationAdapter $configurationAdapter)
    {
        $this->configurationAdapter = $configurationAdapter;
    }

    /**
     * @param int $productId
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getProductAvailability($productId)
    {
        $product = new Product($productId);

        switch ($product->out_of_stock) {
            case 1:
                return ProductItemAvailabilityValues::AVAILABLE_FOR_ORDER;
            case 2:
                $isAvailable = Product::isAvailableWhenOutOfStock($product->out_of_stock);

                return $isAvailable ? ProductItemAvailabilityValues::AVAILABLE_FOR_ORDER : ProductItemAvailabilityValues::OUT_OF_STOCK;
            case 0:
            default:
                return ProductItemAvailabilityValues::DISCONTINUED;
        }
    }
}
