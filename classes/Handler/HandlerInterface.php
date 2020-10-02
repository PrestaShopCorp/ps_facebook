<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

interface HandlerInterface
{
    /**
     * @param string $eventName
     * @param array $event
     *
     * @return void
     */
    public function handleEvent(string $eventName, array $event);
}
