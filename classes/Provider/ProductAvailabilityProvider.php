<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use FacebookAds\Object\Values\ProductItemAvailabilityValues;
use Product;
use StockAvailable;

class ProductAvailabilityProvider implements ProductAvailabilityProviderInterface
{
    /**
     * @param int $productId
     * @param int $productAttributeId
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getProductAvailability($productId, $productAttributeId)
    {
        $product = new Product($productId);

        if ((int) StockAvailable::getQuantityAvailableByProduct($productId, $productAttributeId)) {
            return ProductItemAvailabilityValues::IN_STOCK;
        }

        switch ($product->out_of_stock) {
            case 1:
                return ProductItemAvailabilityValues::AVAILABLE_FOR_ORDER;
            case 2:
                $isAvailable = Product::isAvailableWhenOutOfStock($product->out_of_stock);

                return $isAvailable ? ProductItemAvailabilityValues::AVAILABLE_FOR_ORDER : ProductItemAvailabilityValues::OUT_OF_STOCK;
            case 0:
            default:
                return ProductItemAvailabilityValues::OUT_OF_STOCK;
        }
    }
}
