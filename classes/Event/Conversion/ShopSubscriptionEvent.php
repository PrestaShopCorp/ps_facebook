<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;

class ShopSubscriptionEvent extends AbstractEvent
{
    public function send($params)
    {
        $user = $this->createSdkUserData();
        $customData = (new CustomData())
            ->setContentName(pSQL($params['email']));

        $event = (new Event())
            ->setEventName('Subscribe')
            ->setEventTime(time())
            ->setUserData($user)
            ->setCustomData($customData);

        $events = [];
        $events[] = $event;

        $this->sendEvents($events);
    }
}
