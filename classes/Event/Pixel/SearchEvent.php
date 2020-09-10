<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

class SearchEvent extends BaseEvent implements PixelEventInterface
{
    public function sendToBuffer($buffer, $event)
    {
        $buffer->add('');
    }
}
