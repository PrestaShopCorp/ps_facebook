<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Category;
use Context;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use PrestaShop\Module\Ps_facebook\Utility\ProductCatalogUtility;

class ViewContentEvent extends AbstractEvent
{
    /**
     * @var ToolsAdapter
     */
    private $toolsAdapter;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(
        Context $context,
        $pixelId,
        ToolsAdapter $toolsAdapter,
        ConfigurationAdapter $configurationAdapter
    ) {
        parent::__construct($context, $pixelId);
        $this->toolsAdapter = $toolsAdapter;
        $this->configurationAdapter = $configurationAdapter;
    }

    public function send($params)
    {
        if (empty($this->pixelId)) {
            return;
        }

        if (isset($params['event_type'])){$eventType = $params['event_type'];}
        if (isset($params['event_time'])){$eventTime = $params['event_time'];}
        if (isset($params['user'])){$userData = $params['user'];}
        if (isset($params['custom_data'])){$customData = $params['custom_data'];}
        if (isset($params['event_source_url'])){$eventSourceUrl = $params['event_source_url'];}

        if(isset($customData) && isset($customData['contents'])){$contentData = reset($customData['contents']);}

        if (isset($contentData)) {
            $content = new Content();
            if (isset($contentData['id'])){$content->setProductId($contentData['id']);}
            if (isset($contentData['title'])){$content->setTitle($contentData['title']);}
            if (isset($contentData['category'])){$content->setCategory($contentData['category']);}
            if (isset($contentData['item_price'])){$content->setItemPrice($contentData['item_price']);}
            if (isset($contentData['brand'])){$content->setBrand($contentData['brand']);}
        }
        if (isset($userData)) {
            $user = $this->createSdkUserData($userData);
        }

        if (isset($customData)) {
            $customDataObj = new CustomData();
            if (isset($customData['currency'])){$customDataObj->setCurrency($customData['currency']);}
            /** more about value here: https://www.facebook.com/business/help/392174274295227?id=1205376682832142 */
            if (isset($customData['value'])){$customDataObj->setValue($customData['value']);}
            if (isset($content)){$customDataObj->setContents([$content]);}
            if (isset($customData['content_type'])){$customDataObj->setContentType($customData['content_type']);}
            if (isset($customData['content_name'])){$customDataObj->setContentName($customData['content_name']);}
            if (isset($customData['content_category'])){$customDataObj->setContentCategory($customData['content_category']);}
            if (isset($customData['content_type'])){$customDataObj->setContentType($customData['content_type']);}
            if (isset($customData['content_ids'])){$customDataObj->setContentIds($customData['content_ids']);}
        }

        $event = new Event();
        if (isset($eventType)){$event->setEventName($eventType);}
        if (isset($eventTime)){$event->setEventTime($eventTime);}
        if (isset($user)){$event->setUserData($user);}
        if (isset($customData)){$event->setCustomData($customDataObj);}
        if (isset($eventSourceUrl)){$event->setEventSourceUrl($eventSourceUrl);}

        $events[] = $event;

        if (empty($event)) {
            return true;
        }

        $this->sendEvents($events);
    }
}
