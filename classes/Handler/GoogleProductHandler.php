<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Utility\GoogleProductUtility;

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
            $googleProductObj = GoogleProductUtility::googleProductToObject($googleProductId);
            $googleProductInfo = $this->productRepository->getInformationAboutGoogleProduct(
                $googleProductObj,
                $shopId
            );
            $googleProductsInformation[$googleProductId] = $googleProductInfo ? $googleProductInfo[0] : [];
            $googleProductsInformation[$googleProductId]['message'] = $message;
        }

        return $googleProductsInformation;
    }
}
