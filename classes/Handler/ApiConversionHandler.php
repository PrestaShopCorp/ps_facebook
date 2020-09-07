<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\UserData;
use PrestaShop\Module\PrestashopFacebook\Handler\Conversion\SearchEvent;
use PrestaShop\Module\PrestashopFacebook\Handler\Pixel\ViewContentEvent;

class ApiConversionHandler
{
    private $facebookBusinessSDK;

    public function __construct()
    {
        // TODO: replace with some configuration::getValue()
        Api::init(
            '726899634800479', // app_id
            'b3f469de46ebc1f94f5b8e3e0db09fc4', // app_secret
            '726899634800479|8Bwl57pXa2EdPBQug5QwGUFS0gY' // access_token
        );

        $this->facebookBusinessSDK = Api::instance();
        $this->facebookBusinessSDK->setLogger(new CurlLogger());
    }

    public function handleEvent($eventName, $event)
    {
        // TODO: add logic to handle different event
        switch ($eventName) {
            case 'hookActionSearch':
                (new SearchEvent($this->context))->send($event);
            break;

            case 'hookDisplayHeader':
                (new ViewContentEvent($this->context))->send($event);
            break;

            default:
                // unsupported event
            break;
        }
    }

    /**
     * @return UserData
     */
    private function createSdkUserData()
    {
        return (new UserData())
            ->setFbc('fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890')
            // It is recommended to send Client IP and User Agent for ServerSide API Events.
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setFbp('fb.1.1558571054389.1098115397')
            ->setEmail('joe@eg.com');
    }
}
