<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Context;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use PrestaShop\Module\PrestashopFacebook\Event\Conversion\SearchEvent;

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

    public function handleEvent($eventName, $event)
    {
        // TODO: add logic to handle different event
        switch ($eventName) {
            case 'hookActionSearch':
                 (new SearchEvent($this->context))->send($event);
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
