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
            $eventBusProductInfo = $this->productRepository->getInformationAboutEventBusProduct(
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
     * @param string $lastSyncDate
     * @param int $shopId
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getFilteredInformationAboutEventBusProducts(
        array $eventBusProducts,
        $lastSyncDate,
        $shopId
    ) {
        $formattedSyncTimeDate = date('Y-m-d H:i:s', strtotime($lastSyncDate));
        $productsWithErrors = array_keys($eventBusProducts);
        $eventBusProductsInfo = $this->productRepository->getInformationAboutEventBusProducts(
            $formattedSyncTimeDate,
            $shopId,
            $productsWithErrors
        );

        foreach ($eventBusProducts as $eventBusProductId => $messages) {
            $eventBusProductsInfo[$eventBusProductId]['messages'] = $messages;
        }

        return $eventBusProductsInfo;
    }
}
