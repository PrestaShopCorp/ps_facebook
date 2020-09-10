<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

use PrestaShop\Module\PrestashopFacebook\Event\PixelEventInterface;

class SearchEvent extends BaseEvent implements PixelEventInterface
{
    public function sendToBuffer($buffer, $event)
    {
        $buffer->add('');
    }
}
