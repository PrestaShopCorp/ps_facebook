<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Cart;
use Category;
use Context;
use FacebookAds\Object\ServerSide\Content;
use Order;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;
use PrestaShop\Module\Ps_facebook\Utility\ProductCatalogUtility;
use Product;
use Ps_facebook;

class EventDataProvider
{
    const PRODUCT_TYPE = 'product';

    const CATEGORY_TYPE = 'product_group';

    /**
     * @var Context
     */
    private $context;

    private $locale;

    /**
     * @var int
     */
    private $idLang;

    /**
     * @var ToolsAdapter
     */
    private $toolsAdapter;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ps_facebook
     */
    private $module;

    public function __construct(
        ToolsAdapter $toolsAdapter,
        ConfigurationAdapter $configurationAdapter,
        ProductRepository $productRepository,
        Context $context,
        ps_facebook $module
    ) {
        $this->toolsAdapter = $toolsAdapter;
        $this->context = $context;
        $this->locale = \Tools::strtoupper($this->context->language->iso_code);
        $this->idLang = (int) $this->context->language->id;
        $this->configurationAdapter = $configurationAdapter;
        $this->productRepository = $productRepository;
        $this->module = $module;
    }

    public function generateEventData($name, array $params)
    {
        switch ($name) {
            case 'hookDisplayHeader':
                $controllerPage = $this->context->controller->php_self;
                if (true === \Tools::isSubmit('submitCustomizedData')) {
                    return $this->getCustomEventData($params);
                }
                if ($controllerPage === 'product') {
                    return $this->getProductPageData();
                }
                if ($controllerPage === 'category' && $this->context->controller->controller_type === 'front') {
                    return $this->getCategoryPageData();
                }
                if ($controllerPage === 'cms') {
                    return $this->getCMSPageData();
                }
                break;
            case 'hookActionSearch':
                return $this->getSearchEventData($params);
            case 'hookActionObjectCustomerMessageAddAfter':
                return $this->getContactEventData();
            case 'hookDisplayOrderConfirmation':
                return $this->getOrderConfirmationEvent($params);
            case 'hookDisplayPersonalInformationTop':
                return $this->getInitiateCheckoutEvent();
            case 'hookActionCartSave':
                return $this->getAddToCartEventData();
            case 'hookActionNewsletterRegistrationAfter':
                return $this->getShopSubscriptionEvent($params);
            case 'hookActionCustomerAccountAdd':
                return $this->getCompleteRegistrationEventData();
        }

        return false;
    }

    private function getProductPageData()
    {
        $type = 'ViewContent';

        /** @var \ProductController|\ProductControllerCore $controller */
        $controller = $this->context->controller;
        $product = $controller->getTemplateVarProduct();

        $fbProductId = ProductCatalogUtility::makeProductId(
            $product['id_product'],
            $product['id_product_attribute']
        );

        // todo: url is generated without attribute and doesn't match with pixel url
        $productUrl = $this->context->link->getProductLink($product->id);
        $content = [
            'id' => $fbProductId,
            'title' => \Tools::replaceAccentedChars($product['name']),
            'category' => (new Category($product['id_category_default']))->getName($this->idLang),
            'item_price' => $product['price_amount'],
            'brand' => (new \Manufacturer($product['id_manufacturer']))->name,
        ];
        $customData = [
            'currency' => $this->getCurrency(),
            'contents' => [$content],
            'content_type' => self::PRODUCT_TYPE,
            'value' => $product['price_amount'],
        ];

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
            'event_source_url' => $productUrl,
        ];
    }

    private function getCategoryPageData()
    {
        $type = 'ViewCategory';

        /** @var \CategoryController|\CategoryControllerCore $controller */
        $controller = $this->context->controller;
        $category = $controller->getCategory();

        $page = $this->toolsAdapter->getValue('page');
        $resultsPerPage = $this->configurationAdapter->get('PS_PRODUCTS_PER_PAGE');

        $prods = $category->getProducts($this->idLang, $page, $resultsPerPage);
        $categoryUrl = $this->context->link->getCategoryLink($category->id);

        $breadcrumbs = $controller->getBreadcrumbLinks();
        $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

        $customData = [
            'content_name' => \Tools::replaceAccentedChars($category->name) . ' ' . $this->locale,
            'content_category' => \Tools::replaceAccentedChars($breadcrumb),
            'content_type' => self::CATEGORY_TYPE,
            'content_ids' => array_column($prods, 'id_product'),
        ];

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
            'event_source_url' => $categoryUrl,
        ];
    }

    private function getCMSPageData()
    {
        $type = 'ViewCMS';
        $cms = new \CMS((int) $this->toolsAdapter->getValue('id_cms'), $this->idLang);

        /** @var \CmsController $controller */
        $controller = $this->context->controller;
        $breadcrumbs = $controller->getBreadcrumbLinks();

        $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $customData = [
            'content_name' => \Tools::replaceAccentedChars($cms->meta_title) . ' ' . $this->locale,
            'content_category' => \Tools::replaceAccentedChars($breadcrumb),
            'content_type' => self::PRODUCT_TYPE,
        ];

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getAddToCartEventData()
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
            return false;
        }
        $type = 'AddToCart';
        if ($op) {
            $type = $op === 'up' ? 'IncreaseProductQuantityInCart' : 'DecreaseProductQuantityInCart';
        } elseif ($isDelete) {
            //todo: when removing product from cart this hook gets called twice
            $type = 'RemoveProductFromCart';
            $quantity = null;
        }

        $productName = Product::getProductName($idProduct, $idProductAttribute);
        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $customData = [
            'content_name' => pSQL($productName),
            'content_type' => self::PRODUCT_TYPE,
            'content_ids' => [$idProduct],
            'num_items' => pSQL($quantity),
        ];

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getCompleteRegistrationEventData()
    {
        $type = 'CompleteRegistration';
        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $customData = [
            'content_name' => 'authentication',
        ];

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getContactEventData()
    {
        $type = 'Contact';
        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $customData = [
            'content_name' => $this->context->customer->email,
        ];

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getCustomisationEventData($params)
    {
        $type = 'CustomizeProduct';

        $idLang = (int) $this->context->language->id;
        $productId = $this->toolsAdapter->getValue('id_product');
        $attributeIds = $params['attributeIds'];
        $locale = \Tools::strtoupper($this->context->language->iso_code);
        $customData = $this->getCustomAttributeData($productId, $idLang, $attributeIds, $locale);

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getCustomEventData($params)
    {
        $type = 'CustomizeProduct';

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
        ];
    }

    private function getSearchEventData($params)
    {
        $searchQuery = $params['searched_query'];
        $quantity = $params['total'];

        $type = 'Search';

        $customData = [
            'content_name' => 'searchQuery',
            'search_string' => $searchQuery,
        ];

        if ($quantity) {
            $customData['num_items'] = $quantity;
        }

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getOrderConfirmationEvent($params)
    {
        /** @var Order $order */
        $order = $this->module->psVersionIs17 ? $params['order'] : $params['objOrder'];
        $productList = [];
        foreach ($order->getProducts() as $product) {
            $productList[] = $product['id_product'];
        }

        $type = 'Purchase';

        $customData = [
            'content_name' => 'purchased',
            'order_id' => $order->id,
            'currency' => $this->getCurrency(),
            'content_ids' => $productList,
            'value' => floatval($order->total_paid),
        ];
        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getInitiateCheckoutEvent()
    {
        $type = 'InitiateCheckout';
        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $cart = $this->context->cart;
        $idLang = (int) $this->context->language->id;
        $contents = $this->getProductContent($cart, $idLang);

        $customData = [
            'contents' => $contents,
            'content_type' => 'product',
            'currency' => $this->getCurrency(),
            'value' => $cart->getOrderTotal(false),
        ];

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getShopSubscriptionEvent($params)
    {
        $type = 'Subscribe';
        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $customData = [
            'content_name' => pSQL($params['email']),
        ];

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
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
            $content = [
                'id' => ProductCatalogUtility::makeProductId($product['id_product'], $product['id_product_attribute']),
                'title' => \Tools::replaceAccentedChars($product['name']),
                'category' => (new Category($product['id_category_default']))->getName($idLang),
                'item_price' => $product['price'],
                'quantity' => $product['quantity'],
                'brand' => (new \Manufacturer($product['id_manufacturer']))->name,
            ];
            $contents[] = $content;
        }

        return $contents;
    }

    /**
     * @param int $productId
     * @param int $idLang
     * @param int[] $attributeIds
     * @param string $locale
     *
     * @return array
     *
     * @throws \PrestaShopException
     */
    private function getCustomAttributeData($productId, $idLang, $attributeIds, $locale)
    {
        $attributes = [];
        foreach ($attributeIds as $attributeId) {
            $attributes[] = (new \AttributeCore($attributeId, $idLang))->name;
        }

        $idProductAttribute = $this->productRepository->getIdProductAttributeByIdAttributes(
            $productId,
            $attributeIds
        );

        $psProductId = ProductCatalogUtility::makeProductId($productId, $idProductAttribute);

        return [
            'content_type' => self::PRODUCT_TYPE,
            'content_ids' => [$psProductId],
            'custom_properties' => [
                'custom_attributes' => $attributes,
            ],
        ];
    }

    private function getCurrency()
    {
        return strtolower($this->context->currency->iso_code);
    }
}
