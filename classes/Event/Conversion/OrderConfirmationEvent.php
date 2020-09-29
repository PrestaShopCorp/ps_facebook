<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Category;
use Context;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use Order;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;

class OrderConfirmationEvent extends AbstractEvent
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
        /** @var Order $order */
        $order = $params['order'];
        $langId = $this->toolsAdapter->getValue('id_lang');
        $currencyIsoCode = $this->context->currency->iso_code;
        $user = $this->createSdkUserData($this->context);

        $contents = [];
        foreach ($order->getProducts() as $product) {
            $content = new Content();
            $content
                ->setProductId($product['product_id'])
                ->setTitle($product['product_name'])
                ->setCategory((new Category($product['id_category_default']))->getName($langId))
                ->setItemPrice($product['price'])
                ->setQuantity($product['product_quantity'])
                ->setBrand((new \Manufacturer($product['id_manufacturer']))->name);

            $contents[] = $content;
        }
        $customData = (new CustomData())
            ->setCurrency($currencyIsoCode)
            ->setValue($order->total_paid)
            ->setContents($contents);

        $event = (new Event())
            ->setEventName('Purchase')
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
