<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Context;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use Product;

class AddToCartEvent extends AbstractEvent
{
    private $toolsAdapter;

    public function __construct(Context $context, $pixelId, ToolsAdapter $toolsAdapter)
    {
        parent::__construct($context, $pixelId);
        $this->toolsAdapter = $toolsAdapter;
    }

    public function send($params)
    {
        $action = $this->toolsAdapter->getValue('action');
        $quantity = $this->toolsAdapter->getValue('qty');
        $idProduct = $this->toolsAdapter->getValue('id_product');
        $op = $this->toolsAdapter->getValue('op');
        $isDelete = $this->toolsAdapter->getValue('delete');

        if ($action !== 'update') {
            return true;
        }
        $eventName = 'AddToCart';
        if ($op) {
            $eventName = $op === 'up' ? 'IncreaseProductQuantityInCart' : 'DecreaseProductQuantityInCart';
            $quantity = 1;
        } elseif ($isDelete) {
            //todo: when removing product from cart this hook gets called twice
            $eventName = 'RemoveProductFromCart';
            $quantity = null;
        }

        $productName = Product::getProductName($idProduct);
        $user = $this->createSdkUserData($this->context);
        $customData = (new CustomData())
            ->setContentName(pSQL($productName))
            ->setNumItems(pSQL($quantity));

        $event = (new Event())
            ->setEventName($eventName)
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
