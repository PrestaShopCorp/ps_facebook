<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Category;
use Context;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;
use PrestaShop\Module\Ps_facebook\Utility\ProductCatalogUtility;
use Product;

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
     * @var string
     */
    private $currencyIsoCode;

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

    public function __construct(
        ToolsAdapter $toolsAdapter,
        ConfigurationAdapter $configurationAdapter,
        ProductRepository $productRepository,
        Context $context
    ) {
        $this->toolsAdapter = $toolsAdapter;
        $this->context = $context;
        $this->locale = \Tools::strtoupper($this->context->language->iso_code);
        $this->idLang = (int) $this->context->language->id;
        $this->currencyIsoCode = strtolower($this->context->currency->iso_code);
        $this->configurationAdapter = $configurationAdapter;
        $this->productRepository = $productRepository;
    }

    public function generateEventData($name, array $params)
    {
        switch ($name) {
            case 'hookDisplayHeader':
                $controllerPage = $this->context->controller->php_self;
                //todo: fix customization part
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
                return $this->getSearchEventData();
                break;
            case 'hookActionObjectCustomerMessageAddAfter':
                return $this->getContactEventData();
            case 'hookDisplayOrderConfirmation':
                return $this->getOrderConfirmationEvent();
                break;
            case 'hookDisplayPersonalInformationTop':
                return $this->getInitiateCheckoutEvent();
                break;
            case 'hookActionCartSave':
                return $this->getAddToCartEventData();
            case 'hookActionNewsletterRegistrationAfter':
                break;
            case 'hookActionCustomerAccountAdd':
            case 'hookActionSubmitAccountBefore':
                return $this->getCompleteRegistrationEventData();
        }

        return false;
    }

    private function getProductPageData()
    {
        $type = 'ViewContent';

        /** @var \ProductController $controller */
        $controller = $this->context->controller;
        $product = $controller->getTemplateVarProduct();

        $fbProductId = ProductCatalogUtility::makeProductId(
            $product['id_product'],
            $product['id_product_attribute'],
            $this->locale
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
            'currency' => $this->currencyIsoCode,
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
            return true;
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
            'currency' => $this->context->currency->iso_code,
            'value' => 1,
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
            'userEmail' => $this->context->customer->email,
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

    private function getCustomEventData()
    {
        $type = 'CustomizeProduct';

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
        ];
    }

    private function getSearchEventData()
    {
        $searchQuery = $this->toolsAdapter->getValue('searched_query');
        $quantity = $this->toolsAdapter->getValue('total');

        $type = 'Search';

        $customData = [
            'content_name' => 'searchQuery',
            'search_string' => $searchQuery,
            'total_results' => $quantity,
        ];

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    private function getOrderConfirmationEvent()
    {
        $order = $this->module->psVersionIs17 ? $this->toolsAdapter->getValue('order') : $this->toolsAdapter->getValue('objOrder');
        foreach ($order->getProducts() as $product) {
            $productList[] = $product['id_product'];
        }

        $type = 'Purchase';

        $customData = [
            'content_name' => 'purchased',
            'customerID' => $order->id_customer,
            'orderID' => $order->id,
            'currency' => $this->context->currency->iso_code,
            'content_ids' => implode(',', $productList),
            'value' => $order->total_paid,
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

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
        ];
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

        $psProductId = ProductCatalogUtility::makeProductId($productId, $idProductAttribute, $locale);

        return [
            'content_type' => self::PRODUCT_TYPE,
            'content_ids' => [$psProductId],
            'custom_properties' => [
                'custom_attributes' => $attributes,
            ],
        ];
    }
}
