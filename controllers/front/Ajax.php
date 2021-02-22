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

use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;
use PrestaShop\Module\PrestashopFacebook\Provider\EventDataProvider;

class Ps_facebookAjaxModuleFrontController extends ModuleFrontController
{
    /** @var Ps_facebook */
    public $module;

    public function postProcess()
    {
        if ('CustomizeProduct' === Tools::getValue('action')) {
            return $this->postProcessCustomizeProduct();
        }
    }

    private function postProcessCustomizeProduct()
    {
        $productId = Tools::getValue('id_product');
        $attributeIds = Tools::getValue('attribute_ids');
        if (!$productId || !$attributeIds) {
            return;
        }

        $params = [
            'productId' => $productId,
            'attributeIds' => $attributeIds,
        ];

        /** @var EventDataProvider $eventDataProvider */
        $eventDataProvider = $this->module->getService(EventDataProvider::class);
        $apiConversionHandler = $this->module->getService(ApiConversionHandler::class);

        $eventData = $eventDataProvider->generateEventData('customizeProduct', $params);
        $apiConversionHandler->handleEvent($eventData);
    }
}
