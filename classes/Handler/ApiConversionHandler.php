<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Context;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use PrestaShop\Module\PrestashopFacebook\Event\Conversion\AddToCartEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Conversion\CompleteRegistrationEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Conversion\OrderConfirmationEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Conversion\SearchEvent;
use PrestaShop\Module\PrestashopFacebook\Event\Conversion\ShopSubscriptionEvent;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;

class ApiConversionHandler
{
    /**
     * @var Api|null
     */
    private $facebookBusinessSDK;

    /**
     * @var Context
     */
    private $context;

    public function __construct()
    {
        Api::init(
            null, // app_id
            null, // app_secret
            \Configuration::get('PS_FBE_ACCESS_TOKEN') // access_token
        );

        $this->facebookBusinessSDK = Api::instance();
        $this->facebookBusinessSDK->setLogger(new CurlLogger());
        $this->context = Context::getContext();
    }

    public function handleEvent($eventName, $params)
    {
        $pixelId = \Configuration::get('PS_PIXEL_ID');

        // TODO: add logic to handle different event
        switch ($eventName) {
            case 'hookActionSearch':
                (new SearchEvent($this->context, $pixelId))
                    ->send($params);
                break;
            case 'hookActionCartSave':
                (new AddToCartEvent($this->context, $pixelId, new ToolsAdapter(), new ProductRepository()))
                    ->send($params);
                break;
            case 'hookActionNewsletterRegistrationAfter':
                (new ShopSubscriptionEvent($this->context, $pixelId))
                    ->send($params);
                break;
            case 'hookDisplayOrderConfirmation':
                (new OrderConfirmationEvent($this->context, $pixelId, new ToolsAdapter()))
                    ->send($params);
                break;
            case 'hookActionSubmitAccountBefore':
                (new CompleteRegistrationEvent($this->context, $pixelId))
                    ->send($params);
                break;
            case 'hookDisplayHeader':
                // (new ViewContentEvent($this->context))->send($event);
                break;

            default:
                // unsupported event
                break;
        }
    }
}
