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
            $attributes[] = (new Attribute($attributeId, $idLang))->name;
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
