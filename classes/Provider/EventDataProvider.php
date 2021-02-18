<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Cart;
use Context;
use Order;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use PrestaShop\Module\PrestashopFacebook\API\FacebookCategoryClient;
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

    /**
     * @var ProductAvailabilityProviderInterface
     */
    private $availabilityProvider;

    /**
     * @var FacebookCategoryClient
     */
    private $facebookCategoryClient;

    /**
     * @var GoogleCategoryProvider
     */
    private $googleCategoryProvider;

    public function __construct(
        ToolsAdapter $toolsAdapter,
        ConfigurationAdapter $configurationAdapter,
        ProductRepository $productRepository,
        Context $context,
        ps_facebook $module,
        ProductAvailabilityProviderInterface $availabilityProvider,
        FacebookCategoryClient $facebookCategoryClient,
        GoogleCategoryProvider $googleCategoryProvider
    ) {
        $this->toolsAdapter = $toolsAdapter;
        $this->context = $context;
        $this->locale = \Tools::strtoupper($this->context->language->iso_code);
        $this->configurationAdapter = $configurationAdapter;
        $this->productRepository = $productRepository;
        $this->module = $module;
        $this->availabilityProvider = $availabilityProvider;
        $this->facebookCategoryClient = $facebookCategoryClient;
        $this->googleCategoryProvider = $googleCategoryProvider;
    }

    public function generateEventData($name, array $params)
    {
        switch ($name) {
            case 'hookDisplayHeader':
                $controllerPage = $this->context->controller->php_self;
                if (true === \Tools::isSubmit('submitCustomizedData')) {
                    return $this->getCustomEventData();
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
            case 'customizeProduct':
                return $this->getCustomisationEventData($params);
            case 'hookActionFacebookCallPixel':
                return $this->getCustomEvent($params);
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

        $productUrl = $this->context->link->getProductLink($product['id']);

        $categoryPath = $this->googleCategoryProvider->getCategoryPaths(
            $product['id_category_default'],
            $this->context->language->id,
            $this->context->shop->id
        );
        $content = [
            'id' => $fbProductId,
            'title' => \Tools::replaceAccentedChars($product['name']),
            'category' => $categoryPath['category_path'],
            'item_price' => $product['price_tax_exc'],
            'brand' => (new \Manufacturer($product['id_manufacturer']))->name,
        ];
        $customData = [
            'currency' => $this->getCurrency(),
            'content_ids' => [$fbProductId],
            'contents' => [$content],
            'content_type' => self::PRODUCT_TYPE,
            'value' => $product['price_tax_exc'],
        ];

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);
        $category = $this->getCategory($product['id_category_default']);

        $this->context->smarty->assign(
            [
                'retailer_item_id' => $fbProductId,
                'product_availability' => $this->availabilityProvider->getProductAvailability((int) $product['id_product']),
                'item_group_id' => $category,
            ]
        );

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

        $prods = $category->getProducts($this->context->language->id, $page, $resultsPerPage);
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
        $cms = new \CMS((int) $this->toolsAdapter->getValue('id_cms'), $this->context->language->id);

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

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
        ];
    }

    private function getCustomisationEventData($params)
    {
        $type = 'CombinationProduct';

        $idLang = (int) $this->context->language->id;
        $productId = $this->toolsAdapter->getValue('id_product');
        $attributeIds = $params['attributeIds'];
        $customData = $this->getCustomAttributeData($productId, $idLang, $attributeIds);

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
            'value' => (float) ($order->total_paid_tax_excl),
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
        $contents = $this->getProductContent($cart);

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
     *
     * @return array
     */
    private function getProductContent(Cart $cart)
    {
        $contents = [];
        foreach ($cart->getProducts() as $product) {
            $categoryPath = $this->googleCategoryProvider->getCategoryPaths(
                $product['id_category_default'],
                $this->context->language->id,
                $this->context->shop->id
            );
            $content = [
                'id' => ProductCatalogUtility::makeProductId($product['id_product'], $product['id_product_attribute']),
                'quantity' => $product['quantity'],
                'item_price' => $product['price'],
                'title' => \Tools::replaceAccentedChars($product['name']),
                'brand' => (new \Manufacturer($product['id_manufacturer']))->name,
                'category' => $categoryPath['category_path'],
            ];
            $contents[] = $content;
        }

        return $contents;
    }

    /**
     * @param array $params
     *
     * @return array|null
     */
    private function getCustomEvent($params)
    {
        if (!isset($params['eventName']) || !isset($params['module'])) {
            return null;
        }

        $type = pSQL($params['eventName']);

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $customData = [
            'custom_properties' => [
                'module' => pSQL($params['module']),
            ],
        ];

        if (isset($params['id_product'])) {
            $fbProductId = ProductCatalogUtility::makeProductId(
                $params['id_product'],
                isset($params['id_product_attribute']) ? $params['id_product_attribute'] : 0
            );
            $customData['content_ids']['module'] = $fbProductId;
        }

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
        ];
    }

    /**
     * @param int $productId
     * @param int $idLang
     * @param int[] $attributeIds
     *
     * @return array
     *
     * @throws \PrestaShopException
     */
    private function getCustomAttributeData($productId, $idLang, $attributeIds)
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
        return \Tools::strtolower($this->context->currency->iso_code);
    }

    private function getCategory($categoryId)
    {
        $googleCategory = $this->facebookCategoryClient->getGoogleCategory(
            $categoryId,
            $this->context->shop->id
        );

        return $googleCategory ? $googleCategory['id'] : '';
    }
}
