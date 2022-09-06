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

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Config\Env;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\PrestashopFacebook\Presenter\ModuleUpgradePresenter;
use PrestaShop\Module\PrestashopFacebook\Provider\MultishopDataProvider;
use PrestaShop\Module\PrestashopFacebook\Repository\ShopRepository;
use PrestaShop\PsAccountsInstaller\Installer\Facade\PsAccounts;

class AdminPsfacebookModuleController extends ModuleAdminController
{
    /** @var Ps_facebook */
    public $module;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var Env
     */
    private $env;

    /**
     * @var ModuleUpgradePresenter
     */
    private $moduleUpgradePresenter;

    /**
     * @var MultishopDataProvider
     */
    private $multishopDataProvider;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    public function __construct()
    {
        parent::__construct();
        $this->configurationAdapter = $this->module->getService(ConfigurationAdapter::class);
        $this->env = $this->module->getService(Env::class);
        $this->moduleUpgradePresenter = $this->module->getService(ModuleUpgradePresenter::class);
        $this->multishopDataProvider = $this->module->getService(MultishopDataProvider::class);
        $this->shopRepository = $this->module->getService(ShopRepository::class);
        $this->module->getService(ErrorHandler::class);
        $this->bootstrap = false;
    }

    public function initContent()
    {
        (new PrestaShop\PsAccountsInstaller\Installer\Installer(Config::REQUIRED_PS_ACCOUNTS_VERSION))->install();

        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);

        $this->context->smarty->assign([
            'id_pixel' => pSQL($this->configurationAdapter->get(Config::PS_PIXEL_ID)),
            'access_token' => pSQL($this->configurationAdapter->get('PS_FBE_ACCESS_TOKEN')),
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebook'),
            'pathApp' => (bool) $this->env->get('USE_LOCAL_VUE_APP') ? $this->module->getPathUri() . 'views/js/app.js' : $this->env->get('PSX_FACEBOOK_CDN_URL') . 'app.js',
            'chunkVendor' => (bool) $this->env->get('USE_LOCAL_VUE_APP') ? $this->module->getPathUri() . 'views/js/chunk-vendors.js' : $this->env->get('PSX_FACEBOOK_CDN_URL') . 'chunk-vendors.js',
        ]);

        $defaultCurrency = $this->context->currency;
        $defaultLanguage = $this->context->language;

        if ($externalBusinessId) {
            Media::addJsDef([
                'psFacebookExternalBusinessId' => $externalBusinessId,
            ]);
        }

        $psAccountsData = $this->getPsAccountsData();

        Media::addJsDef([
            // (object) cast is useful for the js when the array is empty
            'contextPsAccounts' => (object) $this->module->getService(PsAccounts::class)
                ->getPsAccountsPresenter()
                ->present($this->module->name),
            'psAccountsToken' => $psAccountsData['psAccountsToken'],
            'defaultCategory' => $this->shopRepository->getDefaultCategoryShop(),
            'psAccountShopInConflict' => $this->multishopDataProvider->isCurrentShopInConflict($this->context->shop),
            'psFacebookAppId' => $this->env->get('PSX_FACEBOOK_APP_ID'),
            'psFacebookFbeUiUrl' => $this->env->get('PSX_FACEBOOK_UI_URL'),
            'psFacebookSegmentId' => $this->env->get('PSX_FACEBOOK_SEGMENT_API_KEY'),
            'psFacebookHealthCheckRoute' => $this->context->link->getModuleLink(
                $this->module->name,
                'apiHealthCheck'
            ),
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
            'psFacebookEnsureTokensExchanged' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'EnsureTokensExchanged',
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
            'psFacebookRunPrevalidationScanRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'RunPrevalidationScan',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetCategoryMappingStatus' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'CategoryMappingCounters',
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
            'psFacebookGetProductsWithErrors' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'GetProductsWithErrors',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetProductSyncReporting' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'GetProductSyncReporting',
                    'ajax' => 1,
                ]
            ),
            'psFacebookGetProductStatuses' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'GetProductStatuses',
                    'ajax' => 1,
                ]
            ),
            'psFacebookExportWholeCatalog' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'ExportWholeCatalog',
                    'ajax' => 1,
                ]
            ),
            'psFacebookRetrieveTokensRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'RetrieveTokens',
                    'ajax' => 1,
                ]
            ),
            'i18nSettings' => [
                'isoCode' => $this->context->language->iso_code,
                'languageLocale' => $this->context->language->language_code,
            ],
            'localeLang' => $this->context->language->locale,
            'psFacebookCurrency' => $defaultCurrency->iso_code,
            'psFacebookTimezone' => $this->configurationAdapter->get('PS_TIMEZONE'),
            'psFacebookLocale' => $defaultLanguage->locale,
            'psFacebookModuleEnabled' => Module::isEnabled($this->module->name),
            'shopDomain' => Tools::getShopDomain(false),
            'shopUrl' => Tools::getShopDomainSsl(true),
            'email' => $this->context->employee->email,
            'psVersion' => _PS_VERSION_,
            'moduleVersion' => $this->module->version,
            'psAccountShopId' => $psAccountsData['psAccountShopId'],
            'psEventBusVersionCheck' => $this->moduleUpgradePresenter->generateModuleDependencyVersionCheck(
                'ps_eventbus',
                Config::REQUIRED_PS_EVENTBUS_VERSION
            ),
            'psAccountsVersionCheck' => $this->moduleUpgradePresenter->generateModuleDependencyVersionCheck(
                'ps_accounts',
                Config::REQUIRED_PS_ACCOUNTS_VERSION
            ),
        ]);
        $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . '/views/templates/admin/app.tpl');

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

    private function getPsAccountsData()
    {
        $data = [
            'psAccountsToken' => '',
            'psAccountShopId' => null,
        ];

        try {
            $psAccountsService = $this->module->getService(PsAccounts::class)->getPsAccountsService();
            $data['psAccountsToken'] = $psAccountsService->getOrRefreshToken();
            $data['psAccountShopId'] = $psAccountsService->getShopUuidV4();
        } catch (Exception $e) {
            // Do nothing
        }

        return $data;
    }
}
