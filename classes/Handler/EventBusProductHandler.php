<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;
use PrestaShop\Module\Ps_facebook\Utility\EventBusProductUtility;

class EventBusProductHandler
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
     * @param array $eventBusProducts
     * @param int $shopId
     * @param string $isoCode
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getInformationAboutEventBusProductsWithErrors(array $eventBusProducts, $shopId, $isoCode)
    {
        $eventBusProductsInformation = [];
        foreach ($eventBusProducts as $eventBusProductId => $messages) {
            $eventBusProductObj = eventBusProductUtility::eventBusProductToObject($eventBusProductId);
            $eventBusProductInfo = $this->productRepository->getInformationAbouteventBusProduct(
                $eventBusProductObj,
                $shopId,
                $isoCode
            );
            $eventBusProductsInformation[$eventBusProductId] = $eventBusProductInfo ? $eventBusProductInfo[0] : [];
            $eventBusProductsInformation[$eventBusProductId]['messages'] = $messages;
        }

        return $eventBusProductsInformation;
    }

    /**
     * @param array $eventBusProducts
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
    public function getFilteredInformationAboutEventBusProducts(
        array $eventBusProducts,
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
        $productsWithErrors = array_keys($eventBusProducts);
        $eventBusProductsInfo = $this->productRepository->getInformationAboutEventBusProducts(
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

        foreach ($eventBusProducts as $eventBusProductId => $messages) {
            $eventBusProductsInfo[$eventBusProductId]['messages'] = $messages;
        }

        return $eventBusProductsInfo;
    }
}
