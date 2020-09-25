<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Category;
use Context;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;

class ViewContentEvent extends AbstractEvent
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
        if (empty($this->pixelId)) {
            return;
        }

        // Asset Manager to be sure the JS is loaded
        /** @var \FrontController|\ProductController|\CategoryController $controller */
        $controller = $this->context->controller;

        $page = $controller->php_self;
        if (empty($page)) {
            $page = \Tools::getValue('controller');
        }
        $page = pSQL($page); // is this really needed ?

        $id_lang = (int) $this->context->language->id;
        $locale = \Tools::strtoupper($this->context->language->iso_code);
        $currency_iso_code = $this->context->currency->iso_code;
        $content_type = 'product';
        $events = [];

        /*
        * Triggers ViewContent product pages
        */
        if ($page === 'product') {
            $type = 'ViewContent';

            $product = $controller->getTemplateVarProduct();

            $content = new Content();
            $content
                ->setProductId($product['id_product'])
                ->setTitle(\Tools::replaceAccentedChars($product['name']))
                ->setCategory((new Category($product['id_category_default']))->getName($id_lang))
                ->setItemPrice($product['price_amount'])
                ->setBrand((new \Manufacturer($product['id_manufacturer']))->name);

            $user = $this->createSdkUserData($this->context);
            $customData = (new CustomData())
                ->setCurrency($currency_iso_code)
                ->setContents([$content]);

            $event = (new Event())
                ->setEventName($type)
                ->setEventTime(time())
                ->setUserData($user)
                ->setCustomData($customData);

            $events[] = $event;
        }

        /*
        * Triggers ViewContent for category pages
        */
        if ($page === 'category' && $controller->controller_type === 'front') {
            $type = 'ViewCategory';
            $category = $controller->getCategory();

            $breadcrumbs = $controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

            $user = $this->createSdkUserData($this->context);
            $customData = (new CustomData())
                ->setContentName(\Tools::replaceAccentedChars($category->name) . ' ' . $locale)
                ->setContentCategory(\Tools::replaceAccentedChars($breadcrumb))
                ->setContentType($content_type);

            $event = (new Event())
                ->setEventName($type)
                ->setEventTime(time())
                ->setUserData($user)
                ->setCustomData($customData);

            $events[] = $event;
        }

        /*
        * Triggers ViewContent for cms pages
        */
        if ($page === 'cms') {
            $type = 'ViewCMS';
            $cms = new \CMS((int) \Tools::getValue('id_cms'), $id_lang);

            $breadcrumbs = $controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

            $user = $this->createSdkUserData($this->context);
            $customData = (new CustomData())
                ->setContentName(\Tools::replaceAccentedChars($cms->meta_title) . ' ' . $locale)
                ->setContentCategory(\Tools::replaceAccentedChars($breadcrumb));

            $event = (new Event())
                ->setEventName($type)
                ->setEventTime(time())
                ->setUserData($user)
                ->setCustomData($customData);

            $events[] = $event;
        }

        /*
        * Triggers InitiateCheckout for checkout page
        */
        if ($page === 'cart' && $this->toolsAdapter->getValue('action') === 'show') {
            $type = 'InitiateCheckout';
            $cart = $this->context->cart;

            $user = $this->createSdkUserData($this->context);
            $contents = [];
            foreach ($cart->getProducts() as $product) {
                $content = new Content();
                $content
                    ->setProductId($product['id_product'])
                    ->setTitle(\Tools::replaceAccentedChars($product['name']))
                    ->setCategory((new Category($product['id_category_default']))->getName($id_lang))
                    ->setItemPrice($product['price'])
                    ->setQuantity($product['quantity'])
                    ->setBrand((new \Manufacturer($product['id_manufacturer']))->name);

                $contents[] = $content;
            }

            $customData = (new CustomData())
                ->setContents($contents)
                ->setValue($cart->getOrderTotal(false))
                ->setCurrency($currency_iso_code);

            $event = (new Event())
                ->setEventName($type)
                ->setEventTime(time())
                ->setUserData($user)
                ->setCustomData($customData);

            $events[] = $event;
        }

        if (empty($event)) {
            return true;
        }

        $request = (new EventRequest($this->pixelId))
            ->setEvents($events);

        return $request->execute();
    }
}
