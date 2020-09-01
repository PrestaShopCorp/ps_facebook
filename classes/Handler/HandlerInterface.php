<?php 

namespace PrestaShop\Module\PrestashopFacebook\Handler;

interface HandlerInterface
{
    public function sendEvent(string $eventName, array $event);
    public function sendViewContentEvent(string $eventName, array $event);
    public function sendSearchEvent(string $eventName, array $event);
}