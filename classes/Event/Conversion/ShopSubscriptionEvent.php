<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;

class ShopSubscriptionEvent extends AbstractEvent
{
    public function send($params)
    {
        $user = $this->createSdkUserData($this->context);
        $customData = (new CustomData())
            ->setContentName(pSQL($params['email']));

        $event = (new Event())
            ->setEventName('Subscribe')
            ->setEventTime(time())
            ->setUserData($user)
            ->setCustomData($customData);

        $events = [];
        $events[] = $event;

        $request = (new EventRequest($this->pixelId))
            ->setEvents($events);

        return $request->execute();
    }
}
