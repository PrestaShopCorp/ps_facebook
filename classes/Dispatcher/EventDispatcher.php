<?php

namespace PrestaShop\Module\PrestashopFacebook\Dispatcher;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
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

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(ApiConversionHandler $apiConversionHandler, PixelHandler $pixelHandler, ConfigurationAdapter $configurationAdapter)
    {
        $this->conversionHandler = $apiConversionHandler;
        $this->pixelHandler = $pixelHandler;
        $this->configurationAdapter = $configurationAdapter;
    }

    /**
     * @param string $name
     * @param array $params
     *
     * @return void
     */
    public function dispatch($name, array $params)
    {
        if (true === (bool) $this->configurationAdapter->get(Config::PS_FACEBOOK_PIXEL_ENABLED)) {
            $this->conversionHandler->handleEvent($name, $params);
            $this->pixelHandler->handleEvent($name, $params);
        }
    }
}
