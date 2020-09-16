<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Event\Pixel\SearchEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Pixel\ViewContentEvent;

class PixelHandler
{
    /**
     * @var \Context
     */
    private $context;

    /**
     * @var \Ps_facebook
     */
    private $module;

    /**
     * @var TemplateBuffer
     */
    private $templateBuffer;

    public function __construct($module)
    {
        $this->context = \Context::getContext();
        $this->module = $module;
        $this->templateBuffer = $module->templateBuffer;
    }

    public function handleEvent($eventName, $event)
    {
        switch ($eventName) {
            case 'hookActionSearch':
                (new SearchEvent($this->context, $this->module))
                    ->sendToBuffer($this->templateBuffer, $event);
            break;

            case 'hookDisplayHeader':
                (new ViewContentEvent($this->context, $this->module))
                    ->sendToBuffer($this->templateBuffer, $event);
            break;

            default:
                // unsupported event
            break;
        }
    }
}
