<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler\Pixel;

class SearchEvent extends BaseEvent
{
    public function sendToBuffer($buffer, $event)
    {
        $buffer->add('');
    }
}
