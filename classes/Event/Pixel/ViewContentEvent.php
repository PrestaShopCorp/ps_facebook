<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Event\PixelEventInterface;

class ViewContentEvent extends BaseEvent implements PixelEventInterface
{
    public function sendToBuffer($buffer, $event)
    {
        $pixel_id = \Configuration::get(Config::PS_PIXEL_ID);
        if (empty($pixel_id)) {
            return;
        }

        // Asset Manager to be sure the JS is loaded
        /** @var \FrontController|\ProductController|\CategoryController $controller */
        $controller = $this->context->controller;
        if (true === $this->module->psVersionIs17) {
            $controller->registerJavascript(
                'front_fb_common',
                $this->module->js_path . 'printpixel.js',
                ['position' => 'bottom', 'priority' => 150]
            );
        } else {
            $controller->addJs(
                $this->module->js_path . 'printpixel.js',
                false
            );
        }

        $type = '';
        $content = [];

        $page = $controller->php_self;
        if (empty($page)) {
            $page = \Tools::getValue('controller');
        }
        $page = pSQL($page); // is this really needed ?

        $id_lang = (int) $this->context->language->id;
        $locale = \Tools::strtoupper($this->context->language->iso_code);
        $currency_iso_code = $this->context->currency->iso_code;
        $content_type = 'product';
        $track = 'track';

        /*
        * Triggers ViewContent product pages
        */
        if ($page === 'product') {
            $type = 'ViewContent';

            if (true === $this->module->psVersionIs17) {
                $prods = $controller->getTemplateVarProduct();

                $content = [
                    'content_name' => \Tools::replaceAccentedChars($prods['name']) . ' ' . $locale,
                    'content_ids' => $prods['id_product'],
                    'content_type' => $content_type,
                    'value' => (float) $prods['price_tax_exc'],
                    'currency' => $currency_iso_code,
                ];
            }
        }

        /*
        * Triggers ViewContent for category pages
        */
        if ($page === 'category' && $controller->controller_type === 'front') {
            $type = 'ViewCategory';
            $category = $controller->getCategory();
            $track = 'trackCustom';

            $page = \Tools::getValue('page');
            $resultsPerPage = \Configuration::get('PS_PRODUCTS_PER_PAGE');
            $prods = $category->getProducts($id_lang, $page, $resultsPerPage);

            if (true === $this->module->psVersionIs17) {
                $breadcrumbs = $controller->getBreadcrumbLinks();
                $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));
            } else {
                $breadcrumbs = [];
                $breadcrumbs['links'][] = [
                    'title' => $this->module->l('Home'),
                    'url' => $this->context->link->getPageLink('index', true),
                ];

                foreach ($controller->getCategory()->getAllParents() as $category) {
                    if ($category->id_parent != 0 && !$category->is_root_category) {
                        $breadcrumbs['links'][] = [
                            'title' => $category->name,
                            'url' => $this->context->link->getCategoryLink($category),
                        ];
                    }
                }

                $breadcrumbs['links'][] = $this->context->link->getCategoryLink($controller->getCategory());
                $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));
            }

            $content = [
                'content_name' => \Tools::replaceAccentedChars($category->name) . ' ' . $locale,
                'content_category' => \Tools::replaceAccentedChars($breadcrumb),
                'content_ids' => array_column($prods, 'id_product'),
                'content_type' => $content_type,
            ];
        }

        /*
        * Triggers ViewContent for cms pages
        */
        if ($page === 'cms') {
            $type = 'ViewCMS';
            $cms = new \CMS((int) \Tools::getValue('id_cms'), $id_lang);

            if (true === $this->module->psVersionIs17) {
                $breadcrumbs = $controller->getBreadcrumbLinks();
                $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));
                $track = 'trackCustom';

                $content = [
                    'content_category' => \Tools::replaceAccentedChars($breadcrumb),
                    'content_name' => \Tools::replaceAccentedChars($cms->meta_title) . ' ' . $locale,
                ];
            } else {
                // TODO: 1.6 unsupported?
            }
        }

        $content = $this->formatPixel($content);

        $smartyVariables = [
            'pixel_fc' => $this->module->front_controller,
            'id_pixel' => $pixel_id,
            'type' => $type,
            'content' => $content,
            'track' => $track,
        ];

        if ($this->context->customer->id) {
            $smartyVariables['userInfos'] = $this->getCustomerInformation();
        }

        $this->context->smarty->assign($smartyVariables);

        $buffer->add($this->module->display($this->module->getfilePath(), '/views/templates/hook/header.tpl'));
    }
}
