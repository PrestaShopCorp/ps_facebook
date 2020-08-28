<?php 

namespace PrestaShop\Module\PrestashopFacebook\Dispatcher;

use PrestaShop\Module\PrestashopFacebook\Handler\PixelHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;

class EventDispatcher
{
    /**
     * @var array<Handler>
     */
    private $handlers;

    public function __construct() 
    {
        $this->handlers = [
            new ApiConversionHandler(),
            new PixelHandler()
        ];
    }

    public function dispatch($event): void
    {
        foreach ($this->handlers as $handler) {
            $handler->sendEvent($event);
        }
    }
}