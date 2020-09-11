<?php

namespace PrestaShop\Module\PrestashopFacebook\Event;

interface PixelEventInterface
{
    public function sendToBuffer($buffer, $event);
}
