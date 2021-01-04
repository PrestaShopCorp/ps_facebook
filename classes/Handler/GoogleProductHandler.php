<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\DTO\GoogleProduct;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use Shop;

class GoogleProductHandler
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Shop
     */
    private $shop;

    public function __construct(ProductRepository $productRepository, Shop $shop)
    {
        $this->productRepository = $productRepository;
        $this->shop = $shop;
    }

    public function getInformationAboutGoogleProducts(array $googleProducts)
    {
        $googleProductsInformation = [];
        foreach ($googleProducts as $googleProductId => $message) {
            $googleProductObj = $this->googleProductToObject($googleProductId);
            $googleProductInfo = $this->productRepository->getInformationAboutGoogleProduct(
                $googleProductObj,
                $this->shop->id
            );
            $googleProductsInformation[$googleProductId] = $googleProductInfo ? $googleProductInfo[0] : [];
            $googleProductsInformation[$googleProductId]['message'] = $message;
        }

        return $googleProductsInformation;
    }

    private function googleProductToObject($googleProduct)
    {
        $googleProductSplitted = explode('-', $googleProduct);
        $googleProductObj = new GoogleProduct();
        $googleProductObj->setProductId((int)$googleProductSplitted[GoogleProduct::POSITION_PRODUCT_ID]);
        $googleProductObj->setProductAttributeId((int)$googleProductSplitted[GoogleProduct::POSITION_PRODUCT_ATTRIBUTE_ID]);
        $googleProductObj->setLandIsoCode($googleProductSplitted[GoogleProduct::POSITION_COUNTRY_ISO_CODE]);

        return $googleProductObj;
    }
}
