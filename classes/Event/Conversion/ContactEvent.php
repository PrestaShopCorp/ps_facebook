<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;

class ContactEvent extends AbstractEvent
{
    public function send($params)
    {
        $user = $this->createSdkUserData($this->context);

        $event = (new Event())
            ->setEventName('Contact')
            ->setEventTime(time())
            ->setUserData($user);

        $events = [];
        $events[] = $event;

        $request = (new EventRequest($this->pixelId))
            ->setEvents($events);

        return $request->execute();
    }
}
