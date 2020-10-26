<?php

use PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter;
use PrestaShop\AccountsAuth\Service\PsAccountsService;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

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
            'id_pixel' => pSQL(Configuration::get(Config::PS_PIXEL_ID)),
            'access_token' => pSQL(Configuration::get('PS_FBE_ACCESS_TOKEN')),
            'pathApp' => $this->module->getPathUri() . 'views/js/app.js',
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebook'),
            'chunkVendor' => $this->module->getPathUri() . 'views/js/chunk-vendors.js',
        ]);

        $defaultCurrency = $this->context->currency;
        $defaultLanguage = $this->context->language;

        Media::addJsDef([
            'contextPsAccounts' => $psAccountPresenter->present(),
            'psAccountsToken' => $psAccountsService->getOrRefreshToken(),
            'psFacebookAppId' => $_ENV['PSX_FACEBOOK_APP_ID'],
            'psFacebookFbeUiUrl' => $_ENV['PSX_FACEBOOK_UI_URL'],
            'psFacebookRetrieveExternalBusinessId' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'RetrieveExternalBusinessId',
                    'ajax' => 1,
                ]
            ),
            'psFacebookPixelActivationRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'ActivatePixel',
                    'ajax' => 1,
                ]
            ),
            'psFacebookFbeOnboardingSaveRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'ConnectToFacebook',
                    'ajax' => 1,
                ]
            ),
            'psFacebookLoadConfigurationRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'Configuration',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetFbContextRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'GetFbContext',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetGoogleCategories' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'UpdateGoogleCategories',
                    'ajax' => 1,
                ]
            ),
            'psFacebookUpdateCategoryMatch' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'UpdateCategoryMatch',
                    'ajax' => 1,
                ]
            ),
            'translations' => (new PsFacebookTranslations($this->module))->getTranslations(),
            'i18nSettings' => [
                'isoCode' => $this->context->language->iso_code,
                'languageLocale' => $this->context->language->language_code,
            ],
            'psFacebookCurrency' => $defaultCurrency->iso_code,
            'psFacebookTimezone' => Configuration::get('PS_TIMEZONE'),
            'psFacebookLocale' => $defaultLanguage->locale,
        ]);
        $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . '/views/templates/admin/app.tpl');

        parent::initContent();
    }

    public function postProcess()
    {
        $id_pixel = Tools::getValue(Config::PS_PIXEL_ID);
        if (!empty($id_pixel)) {
            Configuration::updateValue(Config::PS_PIXEL_ID, $id_pixel);
        }

        $access_token = Tools::getValue(Config::FB_ACCESS_TOKEN);
        if (!empty($access_token)) {
            Configuration::updateValue(Config::FB_ACCESS_TOKEN, $access_token);
        }
    }
}
