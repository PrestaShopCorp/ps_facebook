<?php

use FacebookAds\Object\ServerSide\UserData;
use PrestaShop\Module\PrestashopFacebook\Dispatcher\EventDispatcher;
/**
 * 2007-2020 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Ps_facebook extends Module
{
    const MODULE_ADMIN_CONTROLLERS = [
        'AdminAjaxPsfacebookController',
    ];

    const FRONT_CONTROLLERS = [
        'FrontAjaxFacebookWebhooks',
    ];

    const HOOK_LIST = [
        'displayHeader',
        'actionCustomerAccountAdd',
        'actionObjectContactAddAfter',
        'actionCartSave',
        'actionSearch',
        'displayOrderConfirmation'
    ];

    public $name;
    public $tab;
    public $version;
    public $author;
    public $need_instance;
    public $module_key;
    public $controllerAdmin;
    public $bootstrap;
    public $displayName;
    public $description;
    public $psVersionIs17;
    public $css_path;
    public $docs_path;
    public $confirmUninstall;
    public $ps_versions_compliancy;
    public $compiled_path;
    public $js_path;
    public $configurationList = [
        'fbe_pixel_id',
        'fbe_business_id',
        'fbe_business_manager_id',
        'fbe_access_token',
        'fbe_profiles',
        'fbe_pages',
        'fbe_ad_account_id',
        'fbe_catalog_id',
    ];

    public function __construct()
    {
        $this->name = 'ps_facebook';
        $this->tab = 'advertising_marketing';
        $this->version = '0.1';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        // TODO : $this->module_key = '';

        $this->controllerAdmin = 'AdminAjaxPsfacebook';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ps Facebook');
        $this->description = $this->l('Ps facebook');
        $this->psVersionIs17 = (bool) version_compare(_PS_VERSION_, '1.7', '>=');

        $this->css_path = $this->_path . 'views/css/';
        $this->js_path = $this->_path . 'views/js/';
        $this->docs_path = $this->_path . 'docs/';

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    /**
     * This method is trigger at the installation of the module
     * - install all module tables
     * - set some configuration value
     * - register hook used by the module.
     *
     * @return bool
     */
    public function install()
    {
        return parent::install() &&
            $this->installConfiguration() &&
            $this->registerHook(static::HOOK_LIST) &&
            $this->installTabs();
    }

    /**
     * Install configuration for each shop
     *
     * @return bool
     */
    public function installConfiguration()
    {
        $result = true;

        foreach (\Shop::getShops(false, null, true) as $shopId) {
            foreach ($this->configurationList as $name => $value) {
                if (false === Configuration::hasKey($name, null, null, (int) $shopId)) {
                    $result = $result && Configuration::updateValue(
                        $name,
                        $value,
                        false,
                        null,
                        (int) $shopId
                    );
                }
            }
        }

        return $result;
    }

    /**
     * This method is often use to create an ajax controller
     *
     * @return bool
     */
    public function installTabs()
    {
        $installTabCompleted = true;

        foreach (static::MODULE_ADMIN_CONTROLLERS as $controllerName) {
            if (Tab::getIdFromClassName($controllerName)) {
                continue;
            }

            $tab = new Tab();
            $tab->class_name = $controllerName;
            $tab->active = true;
            $tab->name = array_fill_keys(
                Language::getIDs(false),
                $this->displayName
            );
            $tab->id_parent = -1;
            $tab->module = $this->name;
            $installTabCompleted = $installTabCompleted && $tab->add();
        }

        return $installTabCompleted;
    }

    /**
     * Triggered at the uninstall of the module
     * - erase tables
     * - erase configuration value
     * - unregister hook.
     *
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * Load the configuration form.
     *
     * @return void
     */
    public function getContent()
    {
        $dispatcher = new EventDispatcher();
        dump($dispatcher->dispatch([
            'userData' => $this->createSdkUser()
        ]));
        die;

        $this->context->smarty->assign([
            'pathApp' => $this->_path . 'views/js/main.js',
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebook')
        ]);

        return $this->display(__FILE__, '/views/templates/admin/configuration.tpl');
    }

    /**
     * Load back dependencies.
     *
     * @return void
     */
    public function loadAsset()
    {
        // ¯\_(ツ)_/¯ yet
    }

    public function hookActionCustomerAccountAdd(array $param)
    {
        // TODO: send datas to conversion API
        $this->eventResolver->resolve(__FUNCTION__, $param);
    }

    public function hookDisplayHeader(array $param)
    {
        // TODO: refacto and clean this in another class ?
        $pixel_id = Configuration::get('PS_PIXEL_ID');
        if (empty($pixel_id)) {
            return;
        }

        $page = $this->context->controller->php_self;
        if (empty($page)) {
            $page = Tools::getValue('controller');
            $page = pSQL($page);
            if (empty($page)) {
                return;
            }
        }

        $this->context->controller->addJs([
            $this->_path.'views/js/printpixel.js',
        ]);

        $controller_type = $this->context->controller->controller_type;
        $id_lang = (int)$this->context->language->id;
        $locale = Tools::strtoupper($this->context->language->iso_code);
        $currency_iso_code = $this->context->currency->iso_code;
        $content_type = 'product';
        $track = 'track';

        /**
        * Triggers ViewContent product pages
        */
        if ($page === 'product') {
            $type = 'ViewContent';
            $prods = $this->context->controller->getTemplateVarProduct();

            $content = array(
              'content_name' => Tools::replaceAccentedChars($prods['name']) .' '.$locale,
              'content_ids' => $prods['id_product'],
              'content_type' => $content_type,
              'value' => (float) $prods['price_amount'],
              'currency' => $currency_iso_code,
            );
        }

        /**
        * Triggers ViewContent for category pages
        */
        if ($page === 'category' && $controller_type === 'front') {
            $type = 'ViewCategory';
            $category = $this->context->controller->getCategory();

            $breadcrumbs = $this->context->controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

            $prods = $category->getProducts($id_lang, 1, 10);
            $track = 'trackCustom';

            $content = array(
              'content_name' => Tools::replaceAccentedChars($category->name).' '.$locale,
              'content_category' => Tools::replaceAccentedChars($breadcrumb),
              'content_ids' => array_column($prods, 'id_product'),
              'content_type' => $content_type,
            );
        }

        /**
        * Triggers ViewContent for cms pages
        */
        if ($page === 'cms') {
            $type = 'ViewCMS';
            $cms = new Cms((int)Tools::getValue('id_cms'), $id_lang);

            $breadcrumbs = $this->context->controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));
            $track = 'trackCustom';

            $content = array(
              'content_category' => Tools::replaceAccentedChars($breadcrumb),
              'content_name' => Tools::replaceAccentedChars($cms->meta_title) .' '.$locale,
            );
        }

        /**
        * Triggers Search for result pages
        */
        if ($page === 'search') {
            $type = Tools::ucfirst($page);
            $content = array(
              'search_string' => pSQL(Tools::getValue('s')),
            );
        }

        /**
        * Triggers InitiateCheckout for checkout page
        */
        if ($page === 'cart') {
            $type = 'InitiateCheckout';

            $content = array(
              'num_items' => $this->context->cart->nbProducts(),
              'content_ids' => array_column($this->context->cart->getProducts(), 'id_product'),
              'content_type' => $content_type,
              'value' => (float)$this->context->cart->getOrderTotal(),
              'currency' => $iso_code,
            );
        }

        $content = $this->formatPixel($content);

        Media::addJsDef(array(
            'pixel_fc' => $this->front_controller
        ));

        $smartyVariables = array(
            'id_pixel' => pSQL(Configuration::get('PS_PIXEL_ID')),
            'type' => $type,
            'content' => $content,
            'track' => $track,
        );

        if ($this->context->customer->id) {
            $smartyVariables['userInfos'] = $this->getCustomerInformations();
        }

        $this->context->smarty->assign($smartyVariables);
        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
        
    }

    public function hookActionSearch(array $param)
    {
        // ApiConversion and Pixel with TPL
        if ($this->psVersionIs17) {
            // 1.7 version
        } else {
            // 1.6 version
        }
    }

    public function hookActionCartSave(array $param)
    {
        // TODO: send datas to conversion API
    }

    public function displayOrderConfirmation(array $params): void
    {
        // $params = ['order' => Order $order]
    }

    /**
     * formatPixel
     *
     * @param array $params
     *
     * @return array|false
     */
    private function formatPixel($params)
    {
        // TODO: might need some refacto/clean 
        if (!empty($params)) {
            $format = '{';
            foreach ($params as $key => &$val) {
                if (gettype($val) === 'string') {
                    $format .= $key.': \''.addslashes($val).'\', ';
                } elseif (gettype($val) === 'array') {
                    $format .= $key.': [\'';
                    foreach ($val as &$id) {
                        $format .= (int)$id."', '";
                    }
                    unset($id);
                    $format = Tools::substr($format, 0, -4);
                    $format .= '\'], ';
                } else {
                    $format .= $key.': '.addslashes($val).', ';
                }
            }

            $format = Tools::substr($format, 0, -2);
            $format .= '}';

            return $format;
        }

        return false;
    }

    /**
     * getCustomerInformations
     *
     * @return Array
     */
    private function getCustomerInformations()
    {
        $arrayReturned = array();
        $simpleAddresses = $this->context->customer->getSimpleAddresses();

        if (count($simpleAddresses) > 0) {
            $current = reset($simpleAddresses);
            if ($current['city'] != null) {
                $arrayReturned['ct'] = $current['city'];
            }
            if ($current['country_iso'] != null) {
                $arrayReturned['country'] = $current['country_iso'];
            }
            if ($current['postcode'] != null) {
                $arrayReturned['zp'] = $current['postcode'];
            }
            if ($current['phone'] != null) {
                $arrayReturned['ph'] = $current['phone'];
            }
        };

        $gender = $this->context->customer->id_gender == '1' ? 'm' : 'f';
        $arrayReturned['gender'] = $gender;

        $birthDate = \DateTime::createFromFormat('Y-m-d', $this->context->customer->birthday);
        if ($birthDate instanceof \DateTime) {
            $arrayReturned['db'] = $birthDate->format('Ymd');
        }

        $arrayReturned['ln'] = $this->context->customer->firstname;
        $arrayReturned['fn'] = $this->context->customer->lastname;
        $arrayReturned['em'] = $this->context->customer->email;

        // data structured for pixel
        return $arrayReturned;
    }

    /**
     * @param array $userInfos
     *
     * @return UserData
     */
    private function createSdkUser()
    {
        return (new UserData())
            ->setFbc('fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890')
            // It is recommended to send Client IP and User Agent for ServerSide API Events.
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setFbp('fb.1.1558571054389.1098115397')
            ->setEmail('joe@eg.com');
    }
}
