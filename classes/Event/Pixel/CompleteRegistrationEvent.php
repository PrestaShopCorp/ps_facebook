<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Event\PixelEventInterface;

class CompleteRegistrationEvent extends BaseEvent implements PixelEventInterface
{
    public function sendToBuffer($buffer, $event)
    {
        $type = 'CompleteRegistration';
        $track = 'trackCustom';

        $content = [
            'content_name' => 'authentication',
            'currency' => $this->context->currency->iso_code,
            'value' => 1,
        ];
        $content = $this->formatPixel($content);

        $smartyVariables = [
            'pixel_fc' => $this->module->front_controller,
            'id_pixel' => pSQL(\Configuration::get(Config::PS_PIXEL_ID)),
            'type' => $type,
            'content' => $content,
            'track' => $track,
        ];

        if ($this->context->customer->id) {
            $smartyVariables['userInfos'] = $this->getCustomerInformations();
        }

        $this->context->smarty->assign($smartyVariables);
        $buffer->add($this->module->display($this->module->getfilePath(), '/views/templates/hook/fbTrack.tpl'));
    }
}
