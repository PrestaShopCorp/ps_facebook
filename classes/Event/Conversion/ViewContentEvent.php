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

        $controllerPage = $this->context->controller->php_self;
        if (empty($controllerPage)) {
            $controllerPage = \Tools::getValue('controller');
        }
        $controllerPage = pSQL($controllerPage); // is this really needed ?

        $id_lang = (int) $this->context->language->id;
        $locale = \Tools::strtoupper($this->context->language->iso_code);
        $currency_iso_code = $this->context->currency->iso_code;
        $content_type = 'product';
        $events = [];

        /*
        * Triggers ViewContent product pages
        */
        if ($controllerPage === 'product') {
            $type = 'ViewContent';

            /** @var \ProductController $controller */
            $controller = $this->context->controller;
            $product = $controller->getTemplateVarProduct();

            $fbProductId = ProductCatalogUtility::makeProductId(
                $product['id_product'],
                $product['id_product_attribute'],
                $locale
            );

            // todo: url is generated without attribute and doesn't match with pixel url
            $productUrl = $this->context->link->getProductLink($product->id);
            $content = new Content();
            $content
                ->setProductId($fbProductId)
                ->setTitle(\Tools::replaceAccentedChars($product['name']))
                ->setCategory((new Category($product['id_category_default']))->getName($id_lang))
                ->setItemPrice($product['price_amount'])
                ->setBrand((new \Manufacturer($product['id_manufacturer']))->name);

            $user = $this->createSdkUserData($this->context);
            $customData = (new CustomData())
                ->setCurrency($currency_iso_code)
                ->setContents([$content])
                ->setContentType($content_type);

            $event = (new Event())
                ->setEventName($type)
                ->setEventTime(time())
                ->setUserData($user)
                ->setCustomData($customData)
                ->setEventSourceUrl($productUrl);

            $events[] = $event;
        }

        /*
        * Triggers ViewContent for category pages
        */
        if ($controllerPage === 'category' && $this->context->controller->controller_type === 'front') {
            $type = 'ViewCategory';
            $content_type = 'product_group';

            /** @var \CategoryController $controller */
            $controller = $this->context->controller;
            $category = $controller->getCategory();

            $page = $this->toolsAdapter->getValue('page');
            $resultsPerPage = $this->configurationAdapter->get('PS_PRODUCTS_PER_PAGE');

            $prods = $category->getProducts($id_lang, $page, $resultsPerPage);
            $categoryUrl = $this->context->link->getCategoryLink($category->id);

            $breadcrumbs = $controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

            $user = $this->createSdkUserData($this->context);
            $customData = (new CustomData())
                ->setContentName(\Tools::replaceAccentedChars($category->name) . ' ' . $locale)
                ->setContentCategory(\Tools::replaceAccentedChars($breadcrumb))
                ->setContentType($content_type)
                ->setContentIds(array_column($prods, 'id_product'));

            $event = (new Event())
                ->setEventName($type)
                ->setEventTime(time())
                ->setUserData($user)
                ->setCustomData($customData)
                ->setEventSourceUrl($categoryUrl);

            $events[] = $event;
        }

        /*
        * Triggers ViewContent for cms pages
        */
        if ($controllerPage === 'cms') {
            $type = 'ViewCMS';
            $cms = new \CMS((int) \Tools::getValue('id_cms'), $id_lang);

            /** @var \CmsController $controller */
            $controller = $this->context->controller;
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
        if ($controllerPage === 'cart' && $this->toolsAdapter->getValue('action') === 'show') {
            $type = 'InitiateCheckout';
            $cart = $this->context->cart;

            $user = $this->createSdkUserData($this->context);
            $contents = [];
            foreach ($cart->getProducts() as $product) {
                $fbProductId = ProductCatalogUtility::makeProductId(
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $locale
                );
                $content = new Content();
                $content
                    ->setProductId($fbProductId)
                    ->setTitle(\Tools::replaceAccentedChars($product['name']))
                    ->setCategory((new Category($product['id_category_default']))->getName($id_lang))
                    ->setItemPrice($product['price'])
                    ->setQuantity($product['quantity'])
                    ->setBrand((new \Manufacturer($product['id_manufacturer']))->name);

                $contents[] = $content;
            }

            $customData = (new CustomData())
                ->setContents($contents)
                ->setContentType($content_type)
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

        return $this->sendEvents($events);
    }
}
