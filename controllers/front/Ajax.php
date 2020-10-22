<?php

use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Event\Conversion\CustomisationEvent;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;

class ps_facebookAjaxModuleFrontController extends ModuleFrontController
{
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
        $pixelId = \Configuration::get(Config::PS_PIXEL_ID);

        (new CustomisationEvent($this->context, $pixelId, new ProductRepository()))
            ->send($params);
    }
}
