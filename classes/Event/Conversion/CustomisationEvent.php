<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Attribute;
use Context;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;

class CustomisationEvent extends AbstractEvent
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
        $idLang = (int) $this->context->language->id;
        $customData = $this->getCustomAttributeData($idLang);

        $user = $this->createSdkUserData($this->context);

        $event = (new Event())
            ->setEventName('CustomizeProduct')
            ->setEventTime(time())
            ->setUserData($user)
            ->setCustomData($customData);

        $events = [];
        $events[] = $event;

        $request = (new EventRequest($this->pixelId))
            ->setEvents($events);

        return $request->execute();
    }

    private function getCustomAttributeData($idLang)
    {
        $attributeIds = $this->toolsAdapter->getValue('group');
        $quantity = $this->toolsAdapter->getValue('qty');
        $attributes = [];
        foreach ($attributeIds as $attributeId) {
            $attributes[] = (new Attribute($attributeId, $idLang))->name;
        }

        return (new CustomData())
            ->setCustomProperties(
                [
                    'custom attributes' => $attributes,
                    'quantity' => $quantity,
                ]
            );
    }
}
