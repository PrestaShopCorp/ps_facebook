<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use PrestaShop\Module\PrestashopFacebook\Event\ConversionEventInterface;

class SearchEvent implements ConversionEventInterface
{
    public function send($event)
    {
        // TODO

        // return nothing ?
        // $event = (new Event())
        //     ->setEventName('Purchase')
        //     ->setEventTime(time())
        //     ->setEventSourceUrl('http://jaspers-market.com/product/123')
        //     ->setUserData($this->createSdkUserData());

        // $events = [$event];
        // $request = (new EventRequest('726899634800479'))->setEvents($events);
        // $response = $request->execute();

        return '';
    }
}
