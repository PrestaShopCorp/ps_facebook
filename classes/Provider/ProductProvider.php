<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Category;
use Configuration;
use Currency;
use DateTime;
use Image;
use Manufacturer;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\FacebookProduct;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\PrestashopFacebook\Utility\DateUtility;
use PrestaShop\PrestaShop\Adapter\Entity\Language;
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
        $isOrderOutOfStockAvailable = (bool) Configuration::get('PS_ORDER_OUT_OF_STOCK');
        $products = $this->productRepository->getProductsForFacebook((int) Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(true);
        $facebookProducts = [];

        /** @var array $language */
        foreach ($languages as $language) {
            foreach ($products as $product) {
                $productId = (int) $product['id_product'];
                $facebookProduct = new FacebookProduct();
                $facebookProduct
                    ->setId($this->buildId($product))
                    ->setTitle($this->buildTitle($product))
                    ->setDescription($this->buildDescription($product))
                    ->setAvailability($this->buildAvailability($productId, $isOrderOutOfStockAvailable))
                    ->setInventory($this->buildInventory($product['id_product']))
                    ->setCondition($this->buildCondition($product))
                    ->setPrice($this->buildPrice($productId))
                    ->setLink($this->buildLink($productId))
                    ->setImageLink($this->buildImageLink($productId))
                    ->setBrand($this->buildBrand($productId))
                    ->setAdditionalImageLink($this->buildAdditionalImageLink($productId, $language['id_lang']))
                    ->setAgeGroup(null)
                    ->setColor($this->buildColor($productId, $language['id_lang']))
                    ->setGender($this->buildGender())
                    ->setItemGroupId($this->buildItemGroupId($product))
                    ->setGoogleProductCategory($this->buildGoogleProductCategory($product, $language['id_lang']))
                ;

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
     * @param int $productId
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function buildPrice($productId)
    {
        $product = new Product($productId);
        // todo: need a way to know if price is shown with tax or without
        $price = $product->getPrice(true, null, 2);
        $currency = Currency::getCurrencyInstance((int) Configuration::get('PS_CURRENCY_DEFAULT'));

        return "{$price} {$currency->iso_code}";
    }

    /**
     * @param int $productId
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function buildLink($productId)
    {
        $product = new Product($productId);

        return $product->getLink();
    }

    /**
     * @param int $productId
     *
     * @return string
     */
    private function buildImageLink($productId)
    {
        $productCover = Product::getCover($productId);
        $image = new Image($productCover['id_image']);

        return _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
    }

    /**
     * @param int $productId
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function buildBrand($productId)
    {
        $product = new Product($productId);
        $manufacturer = new Manufacturer($product->id_manufacturer);

        if (!$manufacturer) {
            return 'gtin';
        }

        return $manufacturer->name;
    }

    /**
     * @param int $productId
     * @param int $lang
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function buildAdditionalImageLink($productId, $lang)
    {
        $product = new Product($productId);
        $productImages = $product->getImages($lang);

        $additionalImageLinks = [];
        foreach ($productImages as $productImage) {
            if ($productImage['cover']) {
                continue;
            }
            $image = new Image($productImage['id_image']);
            $imageLink = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
            $additionalImageLinks[] = $imageLink;
        }

        return implode(',', $additionalImageLinks);
    }

    private function buildColor($productId, $langId)
    {
        $product = new Product($productId);
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
}
