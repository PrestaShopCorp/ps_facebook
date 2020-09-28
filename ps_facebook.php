<?php

use Dotenv\Dotenv;
use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Database\Installer;
use PrestaShop\Module\PrestashopFacebook\Database\Uninstaller;
use PrestaShop\Module\PrestashopFacebook\Dispatcher\EventDispatcher;
use PrestaShop\Module\PrestashopFacebook\Repository\TabRepository;

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
        'AdminAjaxPsfacebook',
        'AdminPsfacebookModule',
    ];

    const FRONT_CONTROLLERS = [
        'FrontAjaxFacebookWebhooks',
        'FrontAjaxFacebookAjax',
    ];

    const HOOK_LIST = [
        'displayHeader',
        'actionCustomerAccountAdd',
        'actionObjectContactAddAfter',
        'actionCartSave',
        'actionSearch',
        'displayOrderConfirmation',
        'actionAjaxDieProductControllerDisplayAjaxQuickviewAfter',
        'actionObjectCustomerMessageAddAfter',
        'displayFooter',
        'actionNewsletterRegistrationAfter',
        'actionSubmitAccountBefore',
        'displayPersonalInformationTop',
        'displayBackOfficeHeader',
        'actionBeforeAjaxDieProductControllerdisplayAjaxRefresh',
        'displayPersonalInformationTop',
        'actionFrontControllerSetMedia',
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
        $this->bootstrap = false;

        parent::__construct();

        $this->displayName = $this->l('Ps Facebook');
        $this->description = $this->l('Ps facebook');
        $this->psVersionIs17 = (bool) version_compare(_PS_VERSION_, '1.7', '>=');
        $this->css_path = $this->_path . 'views/css/';
        $this->js_path = $this->_path . 'views/js/';
        $this->docs_path = $this->_path . 'docs/';
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
        $this->front_controller = $this->context->link->getModuleLink(
            $this->name,
            'FrontAjaxPixel',
            [],
            true
        );
        $this->templateBuffer = new TemplateBuffer();
        $this->eventDispatcher = new EventDispatcher($this);

        $dotenv = Dotenv::create(_PS_MODULE_DIR_ . 'ps_facebook/');
        $dotenv->load();
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
            (new PrestaShop\AccountsAuth\Installer\Install())->installPsAccounts() &&
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
        return (new Uninstaller($this, new TabRepository()))->uninstall() &&
            parent::uninstall();
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPsfacebookModule'));
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

    public function hookBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->getPathUri() . 'views/css/admin/menu.css');
    }

    public function hookActionFrontControllerSetMedia()
    {
        Media::addJsDef([
            'ajaxController' => $this->context->link->getModuleLink($this->name, 'Ajax', [], true),
        ]);

        $this->context->controller->addJS("{$this->_path}views/js/front/conversion-api.js");
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
        if (true === $this->context->controller->ajax) {
            return;
        }

        $this->eventDispatcher->dispatch(__FUNCTION__, $params);
    }

    public function hookActionCartSave(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookActionObjectCustomerMessageAddAfter(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookDisplayOrderConfirmation(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookActionNewsletterRegistrationAfter(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookActionSubmitAccountBefore(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookDisplayFooter()
    {
        return $this->templateBuffer->flush();
    }

    public function hookDisplayPersonalInformationTop(array $params)
    {
        if (!$this->isFirstCheckoutStep()) {
            return false;
        }

        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    public function hookActionBeforeAjaxDieProductControllerdisplayAjaxRefresh(array $params)
    {
        $this->eventDispatcher->dispatch(__FUNCTION__, $params);

        return $this->templateBuffer->flush();
    }

    /**
     * Tells if we are in the Payment step from the order tunnel.
     * We use the ReflectionObject because it only exists from Prestashop 1.7.7
     *
     * @return bool
     */
    private function isFirstCheckoutStep()
    {
        $checkoutSteps = $this->getAllOrderSteps();

        /* Get the checkoutPaymentKey from the $checkoutSteps array */
        foreach ($checkoutSteps as $stepObject) {
            if ($stepObject instanceof CheckoutAddressesStep) {
                return (bool) $stepObject->isCurrent();
            }
        }

        return false;
    }

    /**
     * Get all existing Payment Steps from front office.
     * Use ReflectionObject before Prestashop 1.7.7
     * From Prestashop 1.7.7 object checkoutProcess is now public
     *
     * @return array
     */
    private function getAllOrderSteps()
    {
        $isPrestashop177 = version_compare(_PS_VERSION_, '1.7.7.0', '>=');

        if (true === $isPrestashop177) {
            return $this->context->controller->getCheckoutProcess()->getSteps();
        }

        /* Reflect checkoutProcess object */
        $reflectedObject = (new ReflectionObject($this->context->controller))->getProperty('checkoutProcess');
        $reflectedObject->setAccessible(true);

        /* Get Checkout steps data */
        $checkoutProcessClass = $reflectedObject->getValue($this->context->controller);

        return $checkoutProcessClass->getSteps();
    }
}
