<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;
use PrestaShop\Module\Ps_facebook\Utility\GoogleProductUtility;

class GoogleProductHandler
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var PsFacebookTranslations
     */
    private $facebookTranslations;

    public function __construct(
        ProductRepository $productRepository,
        PsFacebookTranslations $facebookTranslations
    ) {
        $this->productRepository = $productRepository;
        $this->facebookTranslations = $facebookTranslations;
    }

    /**
     * @param array $googleProducts
     * @param int $shopId
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getInformationAboutGoogleProductsWithErrors(array $googleProducts, $shopId)
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

    /**
     * @param array $googleProducts
     * @param int $syncTimeStamp
     * @param int $shopId
     * @param int|false $page
     * @param string|false $status
     * @param string|false $sortBy
     * @param string|false $sortTo
     * @param int|false$searchById
     * @param string|false $searchByName
     * @param string|false $searchByMessage
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getFilteredInformationAboutGoogleProducts(
        array $googleProducts,
        $syncTimeStamp,
        $shopId,
        $page,
        $status,
        $sortBy,
        $sortTo,
        $searchById,
        $searchByName,
        $searchByMessage
    ) {
        $formattedSyncTimeDate = date('Y-m-d H:i:s', $syncTimeStamp);
        $productsWithErrors = array_keys($googleProducts);
        $googleProductsInfo = $this->productRepository->getInformationAboutGoogleProducts(
            $formattedSyncTimeDate,
            $shopId,
            $productsWithErrors,
            $page,
            $status,
            $sortBy,
            $sortTo,
            $searchById,
            $searchByName,
            $searchByMessage
        );

        foreach ($googleProducts as $googleProductId => $message) {
            $googleProductsInfo['message'] = $message;
        }

        return $googleProductsInfo;
    }
}
