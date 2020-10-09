<?php

use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;
use PrestaShop\AccountsAuth\Service\PsAccountsService;
use PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter;

class AdminPsfacebookModuleController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = false;
    }

    public function initContent()
    {
        $psAccountPresenter = new PsAccountsPresenter($this->module->name);
        $psAccountsService = new PsAccountsService();

        $this->context->smarty->assign([
            'id_pixel' => pSQL(Configuration::get('PS_PIXEL_ID')),
            'access_token' => pSQL(Configuration::get('PS_FBE_ACCESS_TOKEN')),
            'pathApp' => $this->module->getPathUri() . 'views/js/app.js',
            'fbeApp' => $this->module->getPathUri() . 'views/js/main.js',
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebook'),
            'chunkVendor' => $this->module->getPathUri() . 'views/js/chunk-vendors.js',
        ]);

        Media::addJsDef([
            'contextPsAccounts' => $psAccountPresenter->present(),
            'psAccountsToken' => $psAccountsService->getOrRefreshToken(),

            // TODO Get from DTO
            'psFacebookExternalBusinessId' => Configuration::get('PS_FACEBOOK_EXTERNAL_BUSINESS_ID'),
            // If previous is null, can be generated with the Ajax Admin Controller available here:
            'psFacebookRetrieveExternalBusinessId' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                ['action' => 'retrieveExternalBusinessId']
            ),

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
            ],

            'psFacebookCurrency' => null, // TODO from shop (merchant)
            'psFacebookTimezone' => null, // TODO from shop (merchant)
            'psFacebookLocale' => null, // TODO from shop (merchant)
            'psFacebookPixelActivationRoute' => null, // TODO complete ajax route
            'psFacebookFbeOnboardingSaveRoute' => null, // TODO complete ajax route
            'psFacebookFbeUiUrl' => 'https://facebook.psessentials-integration.net', // TODO by default, use the production URL, but can be overridden by integration URL in a .env (cf ps_metrics)
            'translations' => (new PsFacebookTranslations($this->module))->getTranslations(),
            'i18nSettings' => [
                'isoCode' => $this->context->language->iso_code,
                'languageLocale' => $this->context->language->language_code,
            ],
        ]);
        $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . '/views/templates/admin/app.tpl');

        parent::initContent();
    }

    public function postProcess()
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
}
