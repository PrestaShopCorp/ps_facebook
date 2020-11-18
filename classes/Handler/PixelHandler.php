<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Event\Pixel\CompleteRegistrationEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Pixel\ContactEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Pixel\CustomizeEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Pixel\InitiateCheckoutEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Pixel\OrderConfirmationEvent;
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

    public function handleEvent($eventDataName, $eventData)
    {
        switch ($eventDataName) {
            case 'hookActionSearch':
                (new SearchEvent($this->context, $this->module))
                ->sendToBuffer($this->templateBuffer, $eventData);
            break;

            case 'hookDisplayHeader':
                (new ViewContentEvent($this->context, $this->module))
                ->sendToBuffer($this->templateBuffer, $eventData);
                if (true === \Tools::isSubmit('submitCustomizedData')) {
                    (new CustomizeEvent($this->context, $this->module))
                    ->sendToBuffer($this->templateBuffer, $eventData);
                }
            break;

            case 'hookActionObjectCustomerMessageAddAfter':
                (new ContactEvent($this->context, $this->module))
                    ->sendToBuffer($this->templateBuffer, $eventData);
            break;

            case 'hookDisplayOrderConfirmation':
                (new OrderConfirmationEvent($this->context, $this->module))
                    ->sendToBuffer($this->templateBuffer, $eventData);
            break;

            case 'hookActionCustomerAccountAdd':
                (new CompleteRegistrationEvent($this->context, $this->module))
                ->sendToBuffer($this->templateBuffer, $eventData);
            break;

            case 'hookDisplayPersonalInformationTop':
                (new InitiateCheckoutEvent($this->context, $this->module))
                    ->sendToBuffer($this->templateBuffer, $eventData);
                break;

            default:
                // unsupported event
            break;
        }
    }
}
