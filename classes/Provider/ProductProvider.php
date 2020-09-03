<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Configuration;
use Currency;
use DateTime;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\FacebookProduct;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\PrestashopFacebook\Utility\DateUtility;
use Product;

class ProductProvider implements CatalogProvider
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @return FacebookProduct[]|array
     *
     * @throws \PrestaShopException
     */
    public function getProducts()
    {
        $isOrderOutOfStockAvailable = Configuration::get('PS_ORDER_OUT_OF_STOCK');
        $products = $this->productRepository->getProductsForFacebook(Configuration::get('PS_LANG_DEFAULT'));
        $facebookProducts = [];
        foreach ($products as $product) {
            $facebookProduct = new FacebookProduct();
            $facebookProduct
                ->setId($this->buildId($product))
                ->setTitle($this->buildTitle($product))
                ->setDescription($product['product_description_short'])
                ->setAvailability($this->buildAvailability($product['id_product'], (int) $isOrderOutOfStockAvailable))
                ->setInventory($this->buildInventory($product['id_product']))
                ->setCondition($this->buildCondition($product))
                ->setPrice($this->buildPrice($product['id_product']));

            $facebookProducts[] = $facebookProduct;
        }

        return $facebookProducts;
    }

    /**
     * @param array $product
     *
     * @return mixed
     */
    private function buildId(array $product)
    {
        // todo: Need to find way to generate product id because product has different ids with each attribute
        return $product['reference'];
    }

    /**
     * @param array $product
     *
     * @return string
     */
    private function buildTitle(array $product)
    {
        return implode(
            ' ',
            [
                $product['product_name'],
                $product['manufacturer_name'],
            ]
        );
    }

    /**
     * @param $productId
     * @param $isOrderOutOfStockAvailable
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    private function buildAvailability($productId, $isOrderOutOfStockAvailable)
    {
        $availableStock = \StockAvailableCore::getQuantityAvailableByProduct($productId);

        // todo: move strings to config const
        switch ($isOrderOutOfStockAvailable) {
            case 1:
                if ($availableStock <= 0) {
                    return 'available for order';
                }

                return 'in stock';
            case 0:
                if ($availableStock <= 0) {
                    return 'out of stock';
                }

                return 'in stock';
            default:
                return 'discontinued';
        }
    }

    /**
     * @param $productId
     *
     * @return int
     */
    private function buildInventory($productId)
    {
        return \StockAvailableCore::getQuantityAvailableByProduct($productId);
    }

    private function buildCondition(array $product)
    {
        $productAddDate = new DateTime($product['date_add']);
        $productUpdateDate = new DateTime($product['date_upd']);
        $currentDate = new DateTime();

        if (DateUtility::isDateNewerThenGivenDays($currentDate, $productAddDate)) {
            return 'new';
        }

        if (DateUtility::isDateNewerThenGivenDays($currentDate, $productUpdateDate)) {
            return 'refurbished';
        }

        return 'used';
    }

    private function buildPrice($productId)
    {
        $product = new Product($productId);
        // todo: need a way to know if price is shown with tax or without
        $price = $product->getPrice(true, null, 2);
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        return "{$price} {$currency->iso_code}";
    }
}
