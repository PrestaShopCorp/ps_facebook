<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Context;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;

class FirstCheckoutStepEvent extends AbstractEvent
{
    /**
     * @var ToolsAdapter
     */
    private $toolsAdapter;

    public function __construct(Context $context, $pixelId, ToolsAdapter $toolsAdapter)
    {
        parent::__construct($context, $pixelId);
        $this->toolsAdapter = $toolsAdapter;
    }

    public function send($params)
    {
        $isEditAddress = (bool)$this->toolsAdapter->getValue('editAddress');
        $isEditAddressCancel = (bool)$this->toolsAdapter->getValue('cancelAddress');

        if ($isEditAddress || $isEditAddressCancel) {
            return false;
        }

        $user = $this->createSdkUserData($this->context);

        $event = (new Event())
            ->setEventName('InitiateCheckout')
            ->setEventTime(time())
            ->setUserData($user);

        $events = [];
        $events[] = $event;

        $request = (new EventRequest($this->pixelId))
            ->setEvents($events);

        return $request->execute();
    }
}
