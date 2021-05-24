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
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;
use PrestaShop\Module\Ps_facebook\Utility\ProductCatalogUtility;
use PrestaShopException;
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
     * @var GoogleCategoryRepository
     */
    private $googleCategoryRepository;

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
        GoogleCategoryRepository $googleCategoryRepository,
        GoogleCategoryProvider $googleCategoryProvider
    ) {
        $this->toolsAdapter = $toolsAdapter;
        $this->context = $context;
        $this->locale = \Tools::strtoupper($this->context->language->iso_code);
        $this->configurationAdapter = $configurationAdapter;
        $this->productRepository = $productRepository;
        $this->module = $module;
        $this->availabilityProvider = $availabilityProvider;
        $this->googleCategoryRepository = $googleCategoryRepository;
        $this->googleCategoryProvider = $googleCategoryProvider;
    }

    public function generateEventData($name, array $params)
    {
        switch ($name) {
            case 'hookDisplayHeader':
                if (true === \Tools::isSubmit('submitCustomizedData')) {
                    return $this->getCustomEventData();
                }
                if ($this->context->controller instanceof \ProductControllerCore) {
                    return $this->getProductPageData();
                }
                if ($this->context->controller instanceof \CategoryControllerCore) {
                    return $this->getCategoryPageData();
                }
                if ($this->context->controller instanceof \CmsControllerCore) {
                    return $this->getCMSPageData();
                }
                break;
            case 'hookActionSearch':
                return $this->getSearchEventData($params);
            case 'hookActionObjectCustomerMessageAddAfter':
                return $this->getContactEventData();
            case 'hookDisplayOrderConfirmation':
                return $this->getOrderConfirmationEvent($params);
            case 'InitiateCheckout':
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

        /** @var \ProductControllerCore $controller */
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

        $category = $this->googleCategoryRepository->getGoogleCategoryIdByCategoryId(
            $product['id_category_default'],
            $this->context->shop->id
        ) ?: '';

        $this->context->smarty->assign(
            [
                'retailer_item_id' => $fbProductId,
                'product_availability' => $this->availabilityProvider->getProductAvailability(
                    (int) $product['id_product'],
                    (int) $product['id_product_attribute']
                ),
                'item_group_id' => $category,
            ]
        );

        return [
            'custom_data' => $customData,
            'event_source_url' => $productUrl,
        ] + $this->getCommonData($type);
    }

    private function getCategoryPageData()
    {
        $type = 'ViewCategory';

        /** @var \CategoryControllerCore $controller */
        $controller = $this->context->controller;
        $category = $controller->getCategory();

        $page = $this->toolsAdapter->getValue('page');
        $resultsPerPage = $this->configurationAdapter->get('PS_PRODUCTS_PER_PAGE');

        $prods = $category->getProducts($this->context->language->id, $page, $resultsPerPage);
        $categoryUrl = $this->context->link->getCategoryLink($category->id);

        $breadcrumbs = $controller->getBreadcrumbLinks();
        $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

        $contentIds = [];
        if ($prods) {
            foreach ($prods as $product) {
                $contentIds[] = ProductCatalogUtility::makeProductId(
                    $product['id_product'],
                    $product['id_product_attribute']
                );
            }
        }

        $customData = [
            'content_name' => \Tools::replaceAccentedChars($category->name) . ' ' . $this->locale,
            'content_category' => \Tools::replaceAccentedChars($breadcrumb),
            'content_type' => self::CATEGORY_TYPE,
            'content_ids' => $contentIds ?: null,
        ];

        return [
            'custom_data' => $customData,
            'event_source_url' => $categoryUrl,
        ] + $this->getCommonData($type);
    }

    private function getCMSPageData()
    {
        $type = 'ViewCMS';
        $cms = new \CMS((int) $this->toolsAdapter->getValue('id_cms'), $this->context->language->id);

        /** @var \CmsControllerCore $controller */
        $controller = $this->context->controller;
        $breadcrumbs = $controller->getBreadcrumbLinks();

        $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

        $customData = [
            'content_name' => \Tools::replaceAccentedChars($cms->meta_title) . ' ' . $this->locale,
            'content_category' => \Tools::replaceAccentedChars($breadcrumb),
            'content_type' => self::PRODUCT_TYPE,
        ];

        return [
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
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
            try {
                $idProductAttribute = $this->productRepository->getIdProductAttributeByIdAttributes(
                    $idProduct,
                    $attributeGroups
                );
            } catch (PrestaShopException $e) {
                return false;
            }
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

        $customData = [
            'content_name' => pSQL($productName),
            'content_type' => self::PRODUCT_TYPE,
            'content_ids' => [
                ProductCatalogUtility::makeProductId(
                    $idProduct,
                    $idProductAttribute
                ),
            ],
            'num_items' => pSQL($quantity),
        ];

        return [
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
    }

    private function getCompleteRegistrationEventData()
    {
        $type = 'CompleteRegistration';

        $customData = [
            'content_name' => 'authentication',
        ];

        return [
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
    }

    private function getContactEventData()
    {
        return $this->getCommonData('Contact');
    }

    private function getCustomisationEventData($params)
    {
        $type = 'CombinationProduct';

        $idLang = (int) $this->context->language->id;
        $productId = $this->toolsAdapter->getValue('id_product');
        $attributeIds = $params['attributeIds'];
        $customData = $this->getCustomAttributeData($productId, $idLang, $attributeIds);

        return [
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
    }

    private function getCustomEventData()
    {
        return $this->getCommonData('CustomizeProduct');
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

        return [
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
    }

    private function getOrderConfirmationEvent($params)
    {
        /** @var Order $order */
        $order = $this->module->psVersionIs17 ? $params['order'] : $params['objOrder'];
        $productList = [];
        foreach ($order->getCartProducts() as $product) {
            $productList[] = ProductCatalogUtility::makeProductId(
                $product['id_product'],
                $product['id_product_attribute']
            );
        }

        $type = 'Purchase';

        $customData = [
            'content_name' => 'purchased',
            'order_id' => $order->id,
            'currency' => $this->getCurrency(),
            'content_ids' => $productList,
            'content_type' => self::PRODUCT_TYPE,
            'value' => (float) ($order->total_paid_tax_excl),
        ];

        return [
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
    }

    private function getInitiateCheckoutEvent()
    {
        $type = 'InitiateCheckout';

        $cart = $this->context->cart;
        $contents = $this->getProductContent($cart);

        $customData = [
            'contents' => $contents,
            'content_type' => 'product',
            'currency' => $this->getCurrency(),
            'value' => $cart->getOrderTotal(false),
        ];

        return [
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
    }

    private function getShopSubscriptionEvent($params)
    {
        $type = 'Subscribe';

        $customData = [
            'content_name' => pSQL($params['email']),
        ];

        return [
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
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
            'custom_data' => $customData,
        ] + $this->getCommonData($type);
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

        try {
            $idProductAttribute = $this->productRepository->getIdProductAttributeByIdAttributes(
                $productId,
                $attributeIds
            );
        } catch (PrestaShopException $e) {
            $idProductAttribute = 0;
        }

        return [
            'content_type' => self::PRODUCT_TYPE,
            'content_ids' => [
                ProductCatalogUtility::makeProductId($productId, $idProductAttribute),
            ],
            'custom_properties' => [
                'custom_attributes' => $attributes,
            ],
        ];
    }

    /**
     * Generate the array with data that are used for all events
     *
     * @see https://developers.facebook.com/docs/marketing-api/conversions-api/deduplicate-pixel-and-server-events
     *
     * @param string $eventType
     */
    private function getCommonData($eventType)
    {
        $time = time();

        return [
            'event_type' => $eventType,
            'event_time' => $time,
            'user' => CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer),
            'eventID' => uniqid($eventType . '_' . $time . '_', true),
        ];
    }

    private function getCurrency()
    {
        return \Tools::strtolower($this->context->currency->iso_code);
    }
}
