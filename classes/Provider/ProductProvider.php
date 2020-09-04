<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Category;
use Configuration;
use Context;
use Currency;
use DateTime;
use Manufacturer;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\FacebookProduct;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\PrestashopFacebook\Utility\DateUtility;
use PrestaShop\PrestaShop\Adapter\Entity\Language;
use Product;
use Shop;
use SpecificPrice;

class ProductProvider implements CatalogProvider
{

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $shopId
     *
     * @return ProductProvider
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * @return FacebookProduct[]|array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getProducts()
    {
        $shopId = $this->shopId ?: (int)Configuration::get('PS_SHOP_DEFAULT');
        $isOrderOutOfStockAvailable = (bool)Configuration::get('PS_ORDER_OUT_OF_STOCK');
        $products = $this->productRepository->getProductsForFacebook((int)Configuration::get('PS_LANG_DEFAULT'));
        $currencyId = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        $languages = Language::getLanguages(true);

        $facebookProducts = [];

        /** @var array $language */
        foreach ($languages as $language) {
            foreach ($products as $product) {
                $productId = (int)$product['id_product'];
                $productObj = new Product($productId, false, $language['id_lang']);
                $facebookProduct = new FacebookProduct();
                $facebookProduct
                    ->setId($this->buildId($product))
                    ->setTitle($this->buildTitle($product))
                    ->setDescription($this->buildDescription($product))
                    ->setAvailability($this->buildAvailability($productId, $isOrderOutOfStockAvailable))
                    ->setInventory($this->buildInventory($productId))
                    ->setCondition($this->buildCondition($product))
                    ->setPrice($this->buildPrice($productObj, $currencyId))
                    ->setLink($this->buildLink($productObj))
                    ->setImageLink($this->buildImageLink($productObj))
                    ->setBrand($this->buildBrand($productObj))
                    ->setAdditionalImageLink($this->buildAdditionalImageLink($productObj, $language['id_lang']))
                    ->setAgeGroup(null)
                    ->setColor($this->buildColor($productObj, $language['id_lang']))
                    ->setGender($this->buildGender())
                    ->setItemGroupId($this->buildItemGroupId($product))
                    ->setGoogleProductCategory($this->buildGoogleProductCategory($product, $language['id_lang']))
                    ->setCommerceTaxCategory($this->buildCommerceTaxCategory())
                    ->setMaterial($this->buildMaterial())
                    ->setPattern($this->buildPattern())
                    ->setProductType($this->buildProductType($product, $language['id_lang']))
                    ->setSalePrice($this->buildSalePrice($productObj, $currencyId))
                    ->setSalePriceEffectiveDate($this->buildSalePriceEffectiveDate($productId, $shopId, $currencyId))
                    ->setShipping($this->buildShipping())
                    ->setShippingWeight($this->buildShippingWeight($productObj));

                $facebookProducts[] = $facebookProduct;
            }
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
     * @param array $product
     *
     * @return string
     */
    private function buildDescription(array $product)
    {
        return $product['product_description_short'] ?: $product['product_description'] ?: '';
    }

    /**
     * @param int $productId
     * @param bool $isOrderOutOfStockAvailable
     *
     * @return string
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
     * @param int $productId
     *
     * @return int
     */
    private function buildInventory($productId)
    {
        return \StockAvailableCore::getQuantityAvailableByProduct($productId);
    }

    /**
     * @param array $product
     *
     * @return string
     *
     * @throws \Exception
     */
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

    /**
     * @param Product $product
     * @param int $currencyId
     *
     * @return string
     *
     */
    private function buildPrice(Product $product, $currencyId)
    {
        // todo: need a way to know if price is shown with tax or without
        $price = $product->getPriceWithoutReduct(false, null, 2);
        $currency = Currency::getCurrencyInstance($currencyId);

        return "{$price} {$currency->iso_code}";
    }

    /**
     * @param Product $product
     * @return string
     *
     */
    private function buildLink(Product $product)
    {
        return $product->getLink();
    }

    /**
     * @param Product $product
     * @return string
     */
    private function buildImageLink(Product $product)
    {
        $productCover = Product::getCover($product->id);

        return Context::getContext()->link->getImageLink($product->link_rewrite, $productCover['id_image']);
    }

    /**
     * @param Product $product
     *
     * @return string
     *
     */
    private function buildBrand(Product $product)
    {
        $manufacturer = new Manufacturer($product->id_manufacturer);

        if (!$manufacturer) {
            return 'gtin';
        }

        return $manufacturer->name;
    }

    /**
     * @param Product $product
     * @param int $lang
     *
     * @return string
     *
     */
    private function buildAdditionalImageLink(Product $product, $lang)
    {
        $productImages = $product->getImages($lang);

        $additionalImageLinks = [];
        foreach ($productImages as $productImage) {
            if ($productImage['cover']) {
                continue;
            }
            $imageLink = Context::getContext()->link->getImageLink($product->link_rewrite, $productImage['id_image']);
            $additionalImageLinks[] = $imageLink;
        }

        return implode(',', $additionalImageLinks);
    }

    /**
     * @param Product $product
     * @param int $langId
     *
     * @return string
     */
    private function buildColor(Product $product, $langId)
    {
        $productAttributes = $product->getAttributeCombinations($langId);

        $colors = [];
        foreach ($productAttributes as $productAttribute) {
            if (!$productAttribute['is_color_group'] || !$productAttribute['default_on']) {
                continue;
            }
            $colors[] = $productAttribute['attribute_name'];
        }

        return implode(' ', $colors);
    }

    /**
     * todo: how can we know the gender?
     *
     * @return string
     */
    private function buildGender()
    {
        return 'unisex';
    }

    /**
     * @param array $product
     *
     * @return string
     */
    private function buildItemGroupId(array $product)
    {
        return $product['product_name'];
    }

    /**
     * @param array $product
     * @param int $langId
     *
     * @return array
     */
    private function buildGoogleProductCategory(array $product, $langId)
    {
        $categoryId = $product['id_category_default'];
        $category = new Category($categoryId);
        $parentCategories = $category->getAllParents();

        $googleProductCategory = [];
        /** @var Category $parentCategory */
        foreach ($parentCategories as $parentCategory) {
            $googleProductCategory[$parentCategory->name] = $parentCategory->id_category;
        }
        $googleProductCategory[$category->getName($langId)] = $category->id_category;

        return $googleProductCategory;
    }

    /**
     * @return string ''
     * @todo Need more information when to send what category tax or if its even needed
     */
    private function buildCommerceTaxCategory()
    {
        return '';
    }

    /**
     * @return string ''
     * @todo Need more information or if its even needed
     *
     */
    private function buildMaterial()
    {
        return '';
    }

    /**
     * @return string ''
     * @todo Need more information or if its even needed
     *
     */
    private function buildPattern()
    {
        return '';
    }

    /**
     * @param array $product
     * @param int $langId
     *
     * @return array
     */
    private function buildProductType(array $product, $langId)
    {
        $categoryId = $product['id_category_default'];
        $category = new Category($categoryId);
        $parentCategories = $category->getAllParents();

        $googleProductCategory = [];
        /** @var Category $parentCategory */
        foreach ($parentCategories as $parentCategory) {
            $googleProductCategory[$parentCategory->name] = $parentCategory->id_category;
        }
        $googleProductCategory[$category->getName($langId)] = $category->id_category;

        return $googleProductCategory;
    }

    /**
     * @param Product $product
     * @param int $currencyId
     *
     * @return string
     */
    private function buildSalePrice(Product $product, $currencyId)
    {
        // todo: need a way to know if price is shown with tax or without
        $price = $product->getPrice(true, null, 2);
        $currency = Currency::getCurrencyInstance($currencyId);

        return "{$price} {$currency->iso_code}";
    }

    /**
     * @param int $productId
     * @param int $shopId
     * @param int $currencyId
     *
     * @return string
     * @throws \Exception
     * @todo is it okey if we send date as 00-00-00T00:00:00 if date is not selected?
     */
    private function buildSalePriceEffectiveDate($productId, $shopId, $currencyId)
    {
        $shop = new Shop($shopId);
        $specificPrice = SpecificPrice::getSpecificPrice(
            $productId,
            $shop->id,
            $currencyId,
            0,
            $shop->getGroup()->id,
            1
        );

        $discountDateFrom = DateUtility::formattedDate($specificPrice['from']);
        $discountDateTo = DateUtility::formattedDate($specificPrice['to']);

        return implode('/', [$discountDateFrom, $discountDateTo]);
    }

    /**
     * @return string
     * @todo how can we get shipping price if there can be more then one carrier?
     */
    private function buildShipping()
    {
        return '';
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    private function buildShippingWeight(Product $product)
    {
        $weightUnit = Configuration::get('PS_WEIGHT_UNIT');

        return "{$product->weight} {$weightUnit}";
    }
}
