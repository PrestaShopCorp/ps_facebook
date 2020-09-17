<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use PrestaShop\Module\PrestashopFacebook\Event\ConversionEventInterface;

class SearchEvent implements ConversionEventInterface
{
    public function send($event)
    {
        // TO DO name and source URL
        // $event = (new Event())
        //     ->setEventName('Purchase')
        //     ->setEventTime(time())
        //     ->setEventSourceUrl('http://jaspers-market.com/product/123')
        //     ->setUserData($this->createSdkUserData());

        // $events = [$event];
        // $request = (new EventRequest(\Configuration::get('PS_PIXEL_ID')))->setEvents($events);
        // $response = $request->execute();

        // return $response;
    }
}
