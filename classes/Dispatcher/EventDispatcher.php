<?php

namespace PrestaShop\Module\PrestashopFacebook\Dispatcher;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\PixelHandler;
use PrestaShop\Module\PrestashopFacebook\Provider\EventDataProvider;

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

    /**
     * @var EventDataProvider
     */
    private $eventDataProvider;

    public function __construct(
        ApiConversionHandler $apiConversionHandler,
        PixelHandler $pixelHandler,
        ConfigurationAdapter $configurationAdapter,
        EventDataProvider $eventDataProvider
    ) {
        $this->conversionHandler = $apiConversionHandler;
        $this->pixelHandler = $pixelHandler;
        $this->configurationAdapter = $configurationAdapter;
        $this->eventDataProvider = $eventDataProvider;
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
            $eventData = $this->eventDataProvider->generateEventData($name, $params);

            if ($eventData) {
                $this->conversionHandler->handleEvent($eventData);
            }
            $this->pixelHandler->handleEvent($eventData);
        }
    }
}
