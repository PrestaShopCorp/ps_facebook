<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Context;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use Product;

class AddToCartEvent extends AbstractEvent
{
    /**
     * @var ToolsAdapter
     */
    private $toolsAdapter;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        Context $context,
        $pixelId,
        ToolsAdapter $toolsAdapter,
        ProductRepository $productRepository
    ) {
        parent::__construct($context, $pixelId);
        $this->toolsAdapter = $toolsAdapter;
        $this->productRepository = $productRepository;
    }

    public function send($params)
    {
        $action = $this->toolsAdapter->getValue('action');
        $quantity = $this->toolsAdapter->getValue('qty');
        $idProduct = $this->toolsAdapter->getValue('id_product');
        $op = $this->toolsAdapter->getValue('op');
        $isDelete = $this->toolsAdapter->getValue('delete');
        $idProductAttribute = $this->toolsAdapter->getValue('id_product_attribute');
        $attributeGroups = $this->toolsAdapter->getValue('group');

        if ($attributeGroups) {
            $idProductAttribute = $this->productRepository->getIdProductAttributeByIdAttributes(
                $idProduct,
                $attributeGroups
            );
        }

        if ($action !== 'update') {
            return true;
        }
        $eventName = 'AddToCart';
        if ($op) {
            $eventName = $op === 'up' ? 'IncreaseProductQuantityInCart' : 'DecreaseProductQuantityInCart';
        } elseif ($isDelete) {
            //todo: when removing product from cart this hook gets called twice
            $eventName = 'RemoveProductFromCart';
            $quantity = null;
        }

        $productName = Product::getProductName($idProduct, $idProductAttribute);
        $user = $this->createSdkUserData($this->context);
        $customData = (new CustomData())
            ->setContentIds([$idProduct])
            ->setContentName(pSQL($productName))
            ->setNumItems(pSQL($quantity))
            ->setContentType('product');

        $event = (new Event())
            ->setEventName($eventName)
            ->setEventTime(time())
            ->setUserData($user)
            ->setCustomData($customData);

        $events = [];
        $events[] = $event;

        return $this->sendEvents($events);
    }
}
