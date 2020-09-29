<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;

class CustomisationEvent extends AbstractEvent
{
    public function send($params)
    {
        $idLang = (int) $this->context->language->id;
        $productId = $params['productId'];
        $attributeIds = $params['attributeIds'];
        $customData = $this->getCustomAttributeData($productId, $idLang, $attributeIds);

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

    private function getCustomAttributeData($productId, $idLang, $attributeIds)
    {
        $attributes = [];
        foreach ($attributeIds as $attributeId) {
            $attributes[] = (new \AttributeCore($attributeId, $idLang))->name;
        }

        return (new CustomData())
            ->setContentType('product')
            ->setItemNumber($productId)
            ->setCustomProperties(
                [
                    'custom attributes' => $attributes,
                ]
            );
    }
}
