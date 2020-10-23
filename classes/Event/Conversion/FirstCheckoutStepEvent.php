<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Cart;
use Category;
use Context;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
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
        $isEditAddress = (bool) $this->toolsAdapter->getValue('editAddress');
        $isEditAddressCancel = (bool) $this->toolsAdapter->getValue('cancelAddress');

        // Checking if address in not edited or canceled to avoid duplicated event calls
        if ($isEditAddress || $isEditAddressCancel) {
            return false;
        }

        $user = $this->createSdkUserData();
        $cart = $this->context->cart;
        $idLang = (int) $this->context->language->id;
        $currency_iso_code = $this->context->currency->iso_code;

        $contents = $this->getProductContent($cart, $idLang);

        $customData = (new CustomData())
            ->setContents($contents)
            ->setContentType('product')
            ->setValue($cart->getOrderTotal(false))
            ->setCurrency($currency_iso_code);

        $event = (new Event())
            ->setEventName('InitiateCheckout')
            ->setEventTime(time())
            ->setCustomData($customData)
            ->setUserData($user);

        $events = [];
        $events[] = $event;

        $this->sendEvents($events);
    }

    /**
     * @param Cart $cart
     * @param int $idLang
     *
     * @return Content[]
     */
    private function getProductContent(Cart $cart, $idLang)
    {
        $contents = [];
        foreach ($cart->getProducts() as $product) {
            $content = new Content();
            $content
                ->setProductId($product['id_product'])
                ->setTitle(\Tools::replaceAccentedChars($product['name']))
                ->setCategory((new Category($product['id_category_default']))->getName($idLang))
                ->setItemPrice($product['price'])
                ->setQuantity($product['quantity'])
                ->setBrand((new \Manufacturer($product['id_manufacturer']))->name);

            $contents[] = $content;
        }

        return $contents;
    }
}
