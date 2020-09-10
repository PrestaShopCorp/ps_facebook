<?php

namespace PrestaShop\Module\PrestashopFacebook\Dispatcher;

use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\PixelHandler;

class EventDispatcher
{
    /**
     * @var ApiConversionHandler
     */
    private $conversionHandler;

    /**
     * @var PixelHandler
     */
    private $pixelHandler;

    public function __construct($module)
    {
        $this->conversionHandler = new ApiConversionHandler();
        $this->pixelHandler = new PixelHandler($module);
    }

    /**
     * @param string $name
     * @param array $params
     *
     * @return void
     */
    public function dispatch(string $name, array $params)
    {
        $this->conversionHandler->handleEvent($name, $params);
        $this->pixelHandler->handleEvent($name, $params);
    }
}
