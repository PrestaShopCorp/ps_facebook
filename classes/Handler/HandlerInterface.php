<?php 

namespace PrestaShop\Module\PrestashopFacebook\Handler;

interface HandlerInterface
{
    public function sendEvent(array $event);
}