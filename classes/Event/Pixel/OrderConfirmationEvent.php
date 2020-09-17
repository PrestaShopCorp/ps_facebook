<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

use PrestaShop\Module\PrestashopFacebook\Event\PixelEventInterface;

class OrderConfirmationEvent extends BaseEvent implements PixelEventInterface
{
    public function sendToBuffer($buffer, $event)
    {
        $type = 'OrderConfirmation';
        $track = 'trackCustom';

        $content = [
            'customerID' => $event['order']->id_customer,
            'orderID' => $event['order']->id,
        ];

        $content = $this->formatPixel($content);

        $smartyVariables = [
            'pixel_fc' => $this->module->front_controller,
            'id_pixel' => pSQL(\Configuration::get('PS_PIXEL_ID')),
            'type' => $type,
            'content' => $content,
            'track' => $track,
        ];

        if ($this->context->customer->id) {
            $smartyVariables['userInfos'] = $this->getCustomerInformations();
        }

        $this->context->smarty->assign($smartyVariables);

        $buffer->add($this->module->display($this->module->getfilePath(), '/views/templates/hook/OrderConfirmationEvent.tpl'));
    }
}
