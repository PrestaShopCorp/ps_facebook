<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Event\PixelEventInterface;

class ViewContentEvent extends BaseEvent implements PixelEventInterface
{
    public function sendToBuffer($buffer, $params)
    {
        $pixel_id = \Configuration::get(Config::PS_PIXEL_ID);
        if (empty($pixel_id)) {
            return;
        }
        $track = 'track';

        if (isset($params['event_type'])) {
            $eventType = $params['event_type'];
        }
        if (isset($params['event_time'])) {
            $eventTime = $params['event_time'];
        }
        if (isset($params['user'])) {
            $userData = $params['user'];
        }
        if (isset($params['custom_data'])) {
            $customData = $params['custom_data'];
        }
        if (isset($params['event_source_url'])) {
            $eventSourceUrl = $params['event_source_url'];
        }

        if (isset($customData) && isset($customData['contents'])) {
            $contentData = reset($customData['contents']);
        }

        $content = $this->formatPixel($customData);

        $smartyVariables = [
            'pixel_fc' => $this->module->front_controller,
            'id_pixel' => $pixel_id,
            'type' => $eventType,
            'content' => $content,
            'track' => $track,
        ];

        if ($this->context->customer->id) {
            $smartyVariables['userInfos'] = $this->getCustomerInformation($userData);
        }

        $this->context->smarty->assign($smartyVariables);

        $buffer->add($this->module->display($this->module->getfilePath(), '/views/templates/hook/header.tpl'));
    }
}
