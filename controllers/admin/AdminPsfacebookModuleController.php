<?php

use PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter;
use PrestaShop\AccountsAuth\Service\PsAccountsService;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

class AdminPsfacebookModuleController extends ModuleAdminController
{
    /** @var Ps_facebook */
    public $module;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct()
    {
        parent::__construct();
        /* @var ConfigurationAdapter configurationAdapter */
        $this->configurationAdapter = $this->module->getService(ConfigurationAdapter::class);
        $this->bootstrap = false;
    }

    public function initContent()
    {
        //todo: add module version validation so merchant can see that he needs to upgrade module
        $psAccountPresenter = new PsAccountsPresenter($this->module->name);
        $psAccountsService = new PsAccountsService();
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);

        $this->context->smarty->assign([
            'id_pixel' => pSQL($this->configurationAdapter->get(Config::PS_PIXEL_ID)),
            'access_token' => pSQL($this->configurationAdapter->get('PS_FBE_ACCESS_TOKEN')),
            'pathApp' => $this->module->getPathUri() . 'views/js/app.js',
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebook'),
            'chunkVendor' => $this->module->getPathUri() . 'views/js/chunk-vendors.js',
        ]);

        $defaultCurrency = $this->context->currency;
        $defaultLanguage = $this->context->language;

        if ($externalBusinessId) {
            Media::addJsDef([
                'psFacebookExternalBusinessId' => $externalBusinessId,
            ]);
        }

        Media::addJsDef([
            'contextPsAccounts' => $this->psAccountsHotFix($psAccountPresenter->present()),
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
            'psFacebookFbeOnboardingUninstallRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'DisconnectFromFacebook',
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
            'psFacebookUpdateCategoryMatch' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'UpdateCategoryMatch',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetCategory' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'GetCategory',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetCategories' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'getCategories',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetFeaturesRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'GetFeatures',
                    'ajax' => 1,
                ]
            ),
            'psFacebookUpdateFeatureRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'UpdateFeature',
                    'ajax' => 1,
                ]
            ),
            'psFacebookStartProductSyncRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'requireProductSyncStart',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetCatalogSummaryRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'CatalogSummary',
                    'ajax' => 1,
                ]
            ),
            'psFacebookRetrieveFaq' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'RetrieveFaq',
                    'ajax' => 1,
                ]
            ),
            'psFacebookUpdateConversionApiData' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'UpdateConversionApiData',
                    'ajax' => 1,
                ]
            ),
            'translations' => (new PsFacebookTranslations($this->module))->getTranslations(),
            'i18nSettings' => [
                'isoCode' => $this->context->language->iso_code,
                'languageLocale' => $this->context->language->language_code,
            ],
            'psFacebookCurrency' => $defaultCurrency->iso_code,
            'psFacebookTimezone' => $this->configurationAdapter->get('PS_TIMEZONE'),
            'psFacebookLocale' => $defaultLanguage->locale,
            'shopDomain' => Tools::getShopDomain(false),
            'shopUrl' => Tools::getShopDomainSsl(true),
            'email' => $this->context->employee->email,
            'psVersion' => _PS_VERSION_,
            'moduleVersion' => $this->module->version,
        ]);
        $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . '/views/templates/admin/app.tpl');

        /** @var \PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository $a */
        $a = $this->module->getService(\PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository::class);
        $a->areParentCategoriesMatched($this->context->shop->id);
        parent::initContent();
    }

    public function postProcess()
    {
        $id_pixel = Tools::getValue(Config::PS_PIXEL_ID);
        if (!empty($id_pixel)) {
            $this->configurationAdapter->updateValue(Config::PS_PIXEL_ID, $id_pixel);
        }

        $access_token = Tools::getValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN);
        if (!empty($access_token)) {
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, $access_token);
        }
    }

    /**
     * Quickfix for multishop with PS Accounts.
     * The shop in the Context class is always defined, even if multistore. This means the multistore selector
     * is never displayed at the moment.

     * TODO : Move in https://github.com/PrestaShopCorp/prestashop_accounts_vue_components
     */
    private function psAccountsHotFix($presentedData)
    {
        $presentedData['isShopContext'] = Shop::getContext() === Shop::CONTEXT_SHOP;
        foreach ($presentedData['shops'] as $groupKey => &$shopGroup) {
            foreach ($shopGroup['shops'] as &$shop) {
                $shop['url'] = $this->context->link->getAdminLink(
                    'AdminModules',
                    true,
                    [],
                    [
                        'configure' => $this->module->name,
                        'setShopContext' => 's-' . $shop['id'],
                    ]
                );
            }
        }

        return $presentedData;
    }
}
