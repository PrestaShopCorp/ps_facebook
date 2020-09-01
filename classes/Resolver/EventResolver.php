<?php

namespace PrestaShop\Module\PrestashopFacebook\Resolver;

use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\PixelHandler;

class EventResolver
{
    private $conversionHandler;
    private $pixelHandler;

    public function __construct()
    {
        $this->conversionHandler = new ApiConversionHandler();
        $this->pixelHandler = new PixelHandler();
    }

    /**
     * resolve if event will be sent via js variable in TPL or is handled elsewhere in JS
     *
     * @param string $name
     * @param array $params
     *
     * @return mixed (void or tpl to display)
     */
    public function resolve(string $name, array $params)
    {
        switch ($name) {
            // pixel handled by core js event updateCart
            case 'hookActionCartSave':
                $this->sendConversionEvent($name, $params);
            break;

            default:
                $this->sendConversionEvent($name, $params);

                return $this->sendPixelEvent($name, $params);
            break;
        }
    }

    private function sendConversionEvent($name, $params)
    {
        $this->conversionHandler->sendEvent($name, $params);
    }

    private function sendPixelEvent($name, $params)
    {
        $this->pixelHandler->sendEvent($name, $params);
    }
}
