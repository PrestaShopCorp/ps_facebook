<?php

use Dotenv\Dotenv;
use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Database\Installer;
use PrestaShop\Module\PrestashopFacebook\Database\Uninstaller;
use PrestaShop\Module\PrestashopFacebook\Dispatcher\EventDispatcher;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

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
        'actionAjaxDieProductControllerDisplayAjaxQuickviewAfter',
        'actionObjectCustomerMessageAddAfter',
        'displayFooter',
        'actionNewsletterRegistrationAfter',
        'actionSubmitAccountBefore',
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
        return parent::uninstall() &&
            (new Uninstaller($this))->uninstall();
    }

    public function handleForms()
    {
        $id_pixel = Tools::getValue('PS_PIXEL_ID');
        if (!empty($id_pixel)) {
            Configuration::updateValue('PS_PIXEL_ID', $id_pixel);
        }

        $access_token = Tools::getValue('PS_FBE_ACCESS_TOKEN');
        if (!empty($access_token)) {
            Configuration::updateValue('PS_FBE_ACCESS_TOKEN', $access_token);
        }
    }

    public function getContent()
    {
        $this->handleForms();

        $psAccountPresenter = new PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter($this->name);

        $this->context->smarty->assign([
            'id_pixel' => pSQL(Configuration::get('PS_PIXEL_ID')),
            'access_token' => pSQL(Configuration::get('PS_FBE_ACCESS_TOKEN')),
            'pathApp' => $this->_path . 'views/js/app.js',
            'fbeApp' => $this->_path . 'views/js/main.js',
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebook'),
            'chunkVendor' => $this->_path . 'views/js/chunk-vendors.js',
        ]);

        Media::addJsDef([
            'contextPsAccounts' => $psAccountPresenter->present(),
            'contextPsFacebook' => [
                /* 'email' => 'him@prestashop.com',
                'facebookBusinessManager' => [
                  'name' => 'La Fanchonette',
                  'email' => 'fanchonette@ps.com',
                  'createdAt' => 1601283877000
                ],
                'pixel' => [
                  'name' => 'La Fanchonette Test Pixel',
                  'id' => '1234567890',
                  'lastActive' => 1601283877000,
                  'activated' => true
                ],
                'page' => [
                  'name' => 'La Fanchonette',
                  'likes' => 42,
                  'logo' => null
                ],
                'ads' => [
                  'name' => 'La Fanchonette',
                  'email' => 'fanchonette@ps.com',
                  'createdAt' => 1601283877000
                ],
                'categoriesMatching' => [
                  'sent': false
                ]
                */
            ], // from MySQL once FB onboarding done
            // TODO this one given by the API POST /account/onboard:
            'psFacebookExternalBusinessId' => '0b2f5f57-5190-47e2-8df6-b2f96447ac9f',
            // TODO this one given by \PrestaShop\AccountsAuth\Service\PsAccountsService->getOrRefreshToken()
            'psAccountsToken' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwczovL2lkZW50aXR5dG9vbGtpdC5nb29nbGVhcGlzLmNvbS9nb29nbGUuaWRlbnRpdHkuaWRlbnRpdHl0b29sa2l0LnYxLklkZW50aXR5VG9vbGtpdCIsImlhdCI6MTYwMTY0NzM0MywiZXhwIjoxNjAxNjUwOTQzLCJpc3MiOiJmaXJlYmFzZS1hZG1pbnNkay10ZHZ0cUBwcmVzdGFzaG9wLXJlYWR5LWludGVncmF0aW9uLmlhbS5nc2VydmljZWFjY291bnQuY29tIiwic3ViIjoiZmlyZWJhc2UtYWRtaW5zZGstdGR2dHFAcHJlc3Rhc2hvcC1yZWFkeS1pbnRlZ3JhdGlvbi5pYW0uZ3NlcnZpY2VhY2NvdW50LmNvbSIsInVpZCI6InVNaFhlS0hqQVNadjlRR3FIVXRyUmNpZk4yMzIifQ.OhQvEze9zB0z3aBO4qwKwAZmvZYT1FvKWa9XqJfcRU56sxfJR-xpY2C1DyBmiU6IUEghtdTIH44tvH98ke9eAMFHcduBaP-YPAj7n-oikpmmImN8ctQ7exyiXJBVsZ712AF9JNvs7jpf12ByFdJ2F3CZ6eF7GPLmLXsAlxsZY_rauNU4OBWmZvv8d_8qQvgnGsDjo5XRReTVY_oNDRgn9LO5PIf3oPxDPfEgR1EA7RB94BqRLuVN2exgStD1MGYirIwf-PADmFfCtRXWAyMtqJ0z4fXOqQJSs2ZbqVj5LjYInYWL0UMm5CKTQankNN8xUdc45Ies1qFdFY-eeOSKiQ',
            'psFacebookCurrency' => null, // TODO from shop (merchant)
            'psFacebookTimezone' => null, // TODO from shop (merchant)
            'psFacebookLocale' => null, // TODO from shop (merchant)
            'psFacebookPixelActivationRoute' => null, // TODO complete ajax route
            'psFacebookFbeOnboardingSaveRoute' => null, // TODO complete ajax route
            'psFacebookFbeUiUrl' => 'https://facebook.psessentials-integration.net', // TODO by default, use the production URL, but can be overridden by integration URL in a .env (cf ps_metrics)
            'translations' => (new PsFacebookTranslations($this))->getTranslations(),
            'i18nSettings' => [
                'isoCode' => $this->context->language->iso_code,
                'languageLocale' => $this->context->language->language_code,
            ],
        ]);

        return $this->display(__FILE__, '/views/templates/admin/app.tpl');
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
}
