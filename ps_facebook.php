<?php

use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;
use FacebookAds\Object\ServerSide\EventRequest;
use PrestaShop\Module\PrestashopFacebook\Database\Installer;
use PrestaShop\Module\PrestashopFacebook\Database\Uninstaller;
use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Resolver\EventResolver;
use PrestaShop\Module\PrestashopFacebook\Dispatcher\EventDispatcher;

/*
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
        'displayOrderConfirmation',
        'actionAjaxDieProductControllerDisplayAjaxQuickviewAfter'
    ];

    const CONFIGURATION_LIST = [
        'fbe_pixel_id',
        'fbe_business_id',
        'fbe_business_manager_id',
        'fbe_access_token',
        'fbe_profiles',
        'fbe_pages',
        'fbe_ad_account_id',
        'fbe_catalog_id',
    ];

    public $name;
    /**
     * @var string
     */
    public $tab;
    /**
     * @var string
     */
    public $version;
    /**
     * @var string
     */
    public $author;
    /**
     * @var int
     */
    public $need_instance;
    /**
     * @var string
     */
    public $module_key;
    /**
     * @var string
     */
    public $controllerAdmin;
    /**
     * @var bool
     */
    public $psVersionIs17;
    /**
     * @var string
     */
    public $css_path;
    /**
     * @var string
     */
    public $docs_path;
    /**
     * @var string
     */
    public $js_path;

    /**
     * @var EventDispatcher
     */
    public $eventDispatcher;

    /**
     * @var TemplateBuffer
     */
    public $templateBuffer;

    public $front_controller = null;

    public function __construct()
    {
        $this->name = 'ps_facebook';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
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
        $this->front_controller = $this->context->link->getModuleLink(
            $this->name,
            'FrontAjaxPixel',
            array(),
            true
        );
        $this->templateBuffer = new TemplateBuffer();
        $this->eventDispatcher = new EventDispatcher($this);
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
            (new Installer($this))->install();
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
        return parent::uninstall() &&
            (new Uninstaller($this))->uninstall();
    }

    public function getContent()
    {
        // this return -> (#3) Application does not have the capability to make this API call.
        // $user = (new UserData())
        //     // ->setFbc('fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890')
        //     // It is recommended to send Client IP and User Agent for ServerSide API Events.
        //     ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
        //     ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
        //     // ->setFbp('fb.1.1558571054389.1098115397')
        //     ->setEmail('joe@eg.com');

        // $event = (new Event())
        // ->setEventName('ViewContent')
        // ->setEventTime(time())
        // ->setEventSourceUrl('http://jaspers-market.com/product/123')
        // ->setUserData($user);

        // $events = [$event];
        // $request = (new EventRequest('726899634800479'))->setEvents($events);
        // $response = $request->execute();


        $this->context->smarty->assign([
            'pathApp' => $this->_path . 'views/js/main.js',
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebook'),
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

    /**
     * return __FILE__
     *
     * @return string
     */
    public function getFilePath()
    {
        return __FILE__;
    }

    public function hookActionCustomerAccountAdd(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookDisplayHeader(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    // Handle QuickView (ViewContent)
    public function hookActionAjaxDieProductControllerDisplayAjaxQuickviewAfter($params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookActionSearch(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookActionCartSave(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }
}
