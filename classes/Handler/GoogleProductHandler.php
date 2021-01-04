<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\DTO\GoogleProduct;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;

class GoogleProductHandler
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
     * @param array $googleProducts
     * @param int $shopId
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getInformationAboutGoogleProducts(array $googleProducts, $shopId)
    {
        $googleProductsInformation = [];
        foreach ($googleProducts as $googleProductId => $message) {
            $googleProductObj = $this->googleProductToObject($googleProductId);
            $googleProductInfo = $this->productRepository->getInformationAboutGoogleProduct(
                $googleProductObj,
                $shopId
            );
            $googleProductsInformation[$googleProductId] = $googleProductInfo ? $googleProductInfo[0] : [];
            $googleProductsInformation[$googleProductId]['message'] = $message;
        }

        return $googleProductsInformation;
    }

    /**
     * @param string $googleProduct
     *
     * @return GoogleProduct
     */
    private function googleProductToObject($googleProduct)
    {
        $googleProductSplitted = explode('-', $googleProduct);
        $googleProductObj = new GoogleProduct();
        $googleProductObj->setProductId((int) $googleProductSplitted[GoogleProduct::POSITION_PRODUCT_ID]);
        $googleProductObj->setProductAttributeId((int) $googleProductSplitted[GoogleProduct::POSITION_PRODUCT_ATTRIBUTE_ID]);
        $googleProductObj->setLandIsoCode($googleProductSplitted[GoogleProduct::POSITION_COUNTRY_ISO_CODE]);

        return $googleProductObj;
    }
}
