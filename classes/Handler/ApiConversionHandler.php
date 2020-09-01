<?php 

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;
use FacebookAds\Object\ServerSide\EventRequest;

class ApiconversionHandler
{
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

    public function sendEvent($eventName, $event)
    {
        // TODO: add logic to handle different event 
        switch ($eventName) {
            case 'hookActionSearch':
                $this->sendSearchEvent($event);
            break;
            case 'hookDisplayHeader':
                $this->sendViewContentEvent($event);
            break;
            
            default:
                // $this->send($event);
            break;
        }
    }
    public function sendViewContentEvent($event)
    {
       
    }

    private function sendSearchEvent($event)
    {
        // TODO
    }

    private function send($event): void
    {
        $user_data = $this->createSdkUserData();

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