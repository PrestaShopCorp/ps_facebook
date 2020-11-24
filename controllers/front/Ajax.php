<?php

use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;
use PrestaShop\Module\PrestashopFacebook\Provider\EventDataProvider;

class ps_facebookAjaxModuleFrontController extends ModuleFrontController
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
