<?php

use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use PrestaShop\Module\PrestashopFacebook\Event\Conversion\CustomisationEvent;

class ps_facebookAjaxModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $productId = Tools::getValue('id_product');
        $attributeIds = Tools::getValue('attribute_ids');
        $params = [
            'productId' => $productId,
            'attributeIds' => $attributeIds,
        ];
        $pixelId = \Configuration::get('PS_PIXEL_ID');

        (new CustomisationEvent($this->context, $pixelId))
            ->send($params);
    }
}
