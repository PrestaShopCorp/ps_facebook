<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Context;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;

class CompleteRegistrationEvent extends AbstractEvent
{
    public function __construct(Context $context, $pixelId)
    {
        parent::__construct($context, $pixelId);
    }

    public function send($params)
    {
        $user = $this->createSdkUserData($this->context);
        $customData = (new CustomData())
            ->setContentName('Registration');

        $event = (new Event())
            ->setEventName('CompleteRegistration')
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
