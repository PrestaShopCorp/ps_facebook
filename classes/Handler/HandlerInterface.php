<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

interface HandlerInterface
{
    /**
     * @param string $eventName
     * @param array $event
     *
     * @return string
     */
    public function handleEvent(string $eventName, array $event);
}
