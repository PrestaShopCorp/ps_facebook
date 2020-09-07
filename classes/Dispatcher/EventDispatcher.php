<?php

namespace PrestaShop\Module\PrestashopFacebook\Dispatcher;

use PrestaShop\Module\PrestashopFacebook\Handler\PixelHandler;
use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;

class EventDispatcher
{
    private $conversionHandler;
    private $pixelHandler;

    public function __construct()
    {
        $this->templateBuffer = new TemplateBuffer();
        $this->conversionHandler = new ApiConversionHandler();
        $this->pixelHandler = new PixelHandler($this->templateBuffer);
    }

    /**
     * @param string $name
     * @param array $params
     *
     * @return string (empty string or tpl to display)
     */
    public function dispatcher(string $name, array $params)
    {
        $this->conversionHandler->handleEvent($name, $params);
        $this->pixelHandler->handleEvent($name, $params);

        return $this->templateBuffer->flush();
    }
}
