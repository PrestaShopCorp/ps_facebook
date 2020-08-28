<?php 

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;

class ApiconversionHandler
{
    public function __construct() 
    {
        Api::init(
            '726899634800479', // app_id
            'b3f469de46ebc1f94f5b8e3e0db09fc4', // app_secret and access_token below
            '726899634800479|8Bwl57pXa2EdPBQug5QwGUFS0gY'
        );
        $this->facebookBusinessSDK = Api::instance();
        $this->facebookBusinessSDK->setLogger(new CurlLogger());
    }

    public function sendEvent($event = null)
    {
        // TODO: add logic to handle different event
        $this->send($event);
    }

    private function send($event = null): void
    {
        $user_data = $event['userData'];

        $event = (new Event())
            ->setEventName('Purchase')
            ->setEventTime(time())
            ->setEventSourceUrl('http://jaspers-market.com/product/123')
            ->setUserData($user_data);

        $events = [$event];
        $request = (new EventRequest('726899634800479'))->setEvents($events);
        $response = $request->execute();
        dump($response);
        die;
        // TODO: retry if wrong response?
    }
}