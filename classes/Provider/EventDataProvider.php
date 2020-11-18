<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Category;
use Context;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Adapter\ToolsAdapter;
use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;
use PrestaShop\Module\Ps_facebook\Utility\ProductCatalogUtility;

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

    public function __construct(ToolsAdapter $toolsAdapter, ConfigurationAdapter $configurationAdapter, Context $context)
    {
        $this->toolsAdapter = $toolsAdapter;
        $this->context = $context;
        $this->locale = \Tools::strtoupper($this->context->language->iso_code);
        $this->idLang = (int)$this->context->language->id;
        $this->currencyIsoCode = strtolower($this->context->currency->iso_code);
        $this->configurationAdapter = $configurationAdapter;
    }

    public function generateEventData($name, array $params)
    {
        switch ($name) {
            case 'hookDisplayHeader':
                $controllerPage = $this->context->controller->php_self;
                if ($controllerPage === 'product') {
                    return $this->getProductPageData();
                }
                if ($controllerPage === 'category' && $this->context->controller->controller_type === 'front') {
                    return $this->getCategoryPageData();
                }
                if ($controllerPage === 'cms'){
                    return $this->getCMSPageData();
                }
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
            'value' => $product['price_amount']
        ];

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
            'event_source_url' => $productUrl
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
            'content_ids' => array_column($prods, 'id_product')
        ];

        $user = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        return [
            'event_type' => $type,
            'event_time' => time(),
            'user' => $user,
            'custom_data' => $customData,
            'event_source_url' => $categoryUrl
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
}
