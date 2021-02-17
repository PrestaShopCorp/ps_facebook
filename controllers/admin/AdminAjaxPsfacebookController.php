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
use PrestaShop\Module\PrestashopFacebook\API\FacebookCategoryClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Config\Env;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookCatalogExportException;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookOnboardException;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookPsAccountsUpdateException;
use PrestaShop\Module\PrestashopFacebook\Handler\CategoryMatchHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\ConfigurationHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\EventBusProductHandler;
use PrestaShop\Module\PrestashopFacebook\Manager\FbeFeatureManager;
use PrestaShop\Module\PrestashopFacebook\Provider\AccessTokenProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeFeatureDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\GoogleCategoryProviderInterface;
use PrestaShop\Module\PrestashopFacebook\Provider\ProductSyncReportProvider;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Client\PsApiClient;
use PrestaShop\ModuleLibFaq\Faq;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class AdminAjaxPsfacebookController extends ModuleAdminController
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

    public function __construct()
    {
        parent::__construct();
        $this->configurationAdapter = $this->module->getService(ConfigurationAdapter::class);
        $this->env = $this->module->getService(Env::class);
    }

    public function displayAjaxSaveTokenFbeAccount()
    {
        $token = \Tools::getValue('accessToken');
        $response = $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, $token);

        $this->ajaxDie(json_encode($response));
    }

    /**
     * Receive the Facebook access token, store it in DB then regerate app data
     *
     * @throws PrestaShopException
     */
    public function displayAjaxConnectToFacebook()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);
        $onboardingData = $inputs['onboarding'];

        /** @var ConfigurationHandler $configurationHandler */
        $configurationHandler = $this->module->getService(ConfigurationHandler::class);
        /** @var AccessTokenProvider $accessTokenProvider */
        $accessTokenProvider = $this->module->getService(AccessTokenProvider::class);

        $response = $configurationHandler->handle($onboardingData);
        $accessTokenProvider->refreshTokens();

        $this->ajaxDie(
            json_encode($response)
        );
    }

    public function displayAjaxDisconnectFromFacebook()
    {
        // Disconnect from FB
        /** @var ConfigurationHandler $configurationHandler */
        $configurationHandler = $this->module->getService(ConfigurationHandler::class);
        $configurationHandler->uninstallFbe();

        // Return new FB context
        $this->displayAjaxGetFbContext();
    }

    /**
     * Store in database a boolean for know if customer activate pixel
     */
    public function displayAjaxActivatePixel()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        if (isset($inputs['event_status'])) {
            $pixelStatus = $inputs['event_status'];
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PIXEL_ENABLED, $pixelStatus);
            $this->ajaxDie(json_encode(['success' => true]));
        }

        http_response_code(400);
        $this->ajaxDie(json_encode(['success' => false]));
    }

    public function displayAjaxRetrieveExternalBusinessId()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        if (empty($externalBusinessId)) {
            $client = PsApiClient::create($this->env->get('PSX_FACEBOOK_API_URL'));
            try {
                $response = $client->post(
                    '/account/onboard',
                    [
                        'json' => [
                            // For now, not used, so this is not the final URL. To fix if webhook controller is needed.
                            'webhookUrl' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                        ],
                    ]
                )->json();
            } catch (Exception $e) {
                $errorHandler = ErrorHandler::getInstance();
                $errorHandler->handle(
                    new FacebookOnboardException(
                        'Failed to onboard on facebook',
                        FacebookOnboardException::FACEBOOK_ONBOARD_EXCEPTION,
                        $e
                    ),
                    $e->getCode(),
                    true
                );

                return;
            }

            if (!isset($response['externalBusinessId']) && isset($response['message'])) {
                /** @var ErrorHandler $errorHandler */
                $errorHandler = $this->module->getService(ErrorHandler::class);
                $errorHandler->handle(
                    new FacebookOnboardException(
                        $response['message'],
                        FacebookOnboardException::FACEBOOK_RETRIEVE_EXTERNAL_BUSINESS_ID_EXCEPTION
                    )
                );
            }
            $externalBusinessId = $response['externalBusinessId'];
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID, $externalBusinessId);
        }

        $this->ajaxDie(
            json_encode(
                [
                    'externalBusinessId' => $externalBusinessId,
                ]
            )
        );
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxRequireProductSyncStart()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);
        $turnOn = $inputs['turn_on'];

        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $client = PsApiClient::create($this->env->get('PSX_FACEBOOK_API_URL'));
        $client->post(
            '/account/' . $externalBusinessId . '/start_product_sync',
            [
                'json' => ['turnOn' => $turnOn],
            ]
        )->json();

        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START, true);
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PRODUCT_SYNC_ON, $turnOn);

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
                    'turnOn' => $turnOn,
                ]
            )
        );
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxGetFbContext()
    {
        /** @var FbeDataProvider $fbeDataProvider */
        $fbeDataProvider = $this->module->getService(FbeDataProvider::class);

        /** @var FacebookDataProvider $facebookDataProvider */
        $facebookDataProvider = $this->module->getService(FacebookDataProvider::class);
        $facebookContext = $facebookDataProvider->getContext($fbeDataProvider->getFbeData());

        $this->ajaxDie(
            json_encode(
                [
                    'psFacebookExternalBusinessId' => $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID),
                    'contextPsFacebook' => $facebookContext,
                ]
            )
        );
    }

    public function displayAjaxUpdateCategoryMatch()
    {
        /** @var CategoryMatchHandler $categoryMatchHandler */
        $categoryMatchHandler = $this->module->getService(CategoryMatchHandler::class);

        $categoryId = (int) Tools::getValue('category_id');
        $googleCategoryId = (int) Tools::getValue('google_category_id');
        $updateChildren = (bool) Tools::getValue('update_children');
        $shopId = $this->context->shop->id;
        if (!$categoryId || !$googleCategoryId) {
            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                        'message' => 'Missing data',
                    ]
                )
            );
        }
        try {
            $categoryMatchHandler->updateCategoryMatch($categoryId, $googleCategoryId, $updateChildren, $shopId);
        } catch (Exception $e) {
            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                    ]
                )
            );
        }

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
                ]
            )
        );
    }

    public function displayAjaxGetCategory()
    {
        $categoryId = Tools::getValue('id_category');
        /** @var GoogleCategoryProviderInterface $googleCategoryProvider */
        $googleCategoryProvider = $this->module->getService(GoogleCategoryProviderInterface::class);
        $shopId = $this->context->shop->id;

        $googleCategory = $googleCategoryProvider->getGoogleCategory($categoryId, $shopId);
        // FIXME : for now, this function will call our API to get taxonomy details about a category ID.
        //  The needed feature is totally different : see ticket http://forge.prestashop.com/browse/EMKTG-305

        $this->ajaxDie(
            json_encode($googleCategory)
        );
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxGetFeatures()
    {
        /** @var FbeFeatureDataProvider $fbeFeatureDataProvider */
        $fbeFeatureDataProvider = $this->module->getService(FbeFeatureDataProvider::class);

        $fbeFeatures = $fbeFeatureDataProvider->getFbeFeatures();

        $this->ajaxDie(
            json_encode(
                [
                    'fbeFeatures' => $fbeFeatures,
                ]
            )
        );
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxUpdateFeature()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        $featureManager = $this->module->getService(FbeFeatureManager::class);

        $response = $featureManager->updateFeature($inputs['featureName'], $inputs['enabled']);

        if (is_array($response)) {
            $this->ajaxDie(
                json_encode(
                    $response
                )
            );
        } else {
            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                    ]
                )
            );
        }
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxCatalogSummary()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->module->getService(ProductRepository::class);

        /** @var FacebookDataProvider $facebookDataProvider */
        $facebookDataProvider = $this->module->getService(FacebookDataProvider::class);
        $productCount = $facebookDataProvider->getProductsInCatalogCount();
        $productsWithErrors = $productRepository->getProductsWithErrors($this->context->shop->id);
        $productsTotal = $productRepository->getProductsTotal($this->context->shop->id, ['onlyActive' => true]);

        /** @var ProductSyncReportProvider $productSyncReportProvider */
        $productSyncReportProvider = $this->module->getService(ProductSyncReportProvider::class);
        $syncReport = $productSyncReportProvider->getProductSyncReport();
        $productsErrors = isset($syncReport['errors']) ? $syncReport['errors'] : [];
        $lastFinishedSyncStartedAt = isset($syncReport['lastFinishedSyncStartedAt']) ? $syncReport['lastFinishedSyncStartedAt'] : 0;

        $this->ajaxDie(
            json_encode(
                [
                    'exportDone' => (true == $this->configurationAdapter->get(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START)),
                    'exportOn' => (true == $this->configurationAdapter->get(Config::PS_FACEBOOK_PRODUCT_SYNC_ON)),
                    'matchingDone' => false, // true if a category match has been called once (at least 1 matching done)
                    'matchingProgress' => ['total' => 42, 'matched' => 0],
                    'validation' => [
                        'prevalidation' => [
                            'syncable' => $productsTotal - count($productsWithErrors),
                            'notSyncable' => count($productsWithErrors),
                            'errors' => array_slice($productsWithErrors, 0, 20), // only 20 first errors.
                        ],
                        'reporting' => [
                            'lastSyncDate' => $lastFinishedSyncStartedAt,
                            'catalog' => $productCount['product_count'],
                            'errored' => count($productsErrors), // no distinction for base lang vs l10n errors
                        ],
                    ],
                ]
            )
        );
    }

    public function displayAjaxGetCategories()
    {
        $categoryId = (int) Tools::getValue('id_category');
        $page = (int) Tools::getValue('page');
        $shopId = (int) $this->context->shop->id;

        /** @var GoogleCategoryProviderInterface $googleCategoryProvider */
        $googleCategoryProvider = $this->module->getService(GoogleCategoryProviderInterface::class);
        $googleCategories = $googleCategoryProvider->getGoogleCategoryChildren($categoryId, $page, $shopId);

        $this->ajaxDie(
            json_encode($googleCategories)
        );
    }

    public function displayAjaxGetCategoriesByIds()
    {
        $categoryIds = Tools::getValue('id_categories');

        /** @var FacebookCategoryClient $facebookCategoryClient */
        $facebookCategoryClient = $this->module->getService(FacebookCategoryClient::class);
        $googleCategories = $facebookCategoryClient->getGoogleCategories($categoryIds);

        $this->ajaxDie(
            json_encode($googleCategories)
        );
    }

    /****************
     * HELP CONTENT *
     ****************/

    /**
     * Retrieve the faq
     */
    public function displayAjaxRetrieveFaq()
    {
        $faq = new Faq($this->module->module_key, _PS_VERSION_, $this->context->language->iso_code);

        $this->ajaxDie(
            json_encode(
                [
                    'faq' => $faq->getFaq(),
                    'doc' => $this->getReadme(),
                    'contactUs' => 'support-facebook@prestashop.com',
                ]
            )
        );
    }

    /******************************
     * DEVELOPMENT & DEBUG ROUTES *
     ******************************/

    public function displayAjaxUpdateConversionApiData()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);
        $success = true;

        if (isset($inputs['system_access_token'])) {
            $success = /*$success && */
                $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN, $inputs['system_access_token']);
        }
        if (isset($inputs['test_event'])) {
            $success = $success &&
                $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_CAPI_TEST_EVENT_CODE, $inputs['test_event']);
        }
        if (isset($inputs['drop_test_event'])) {
            $success = $success &&
                $this->configurationAdapter->deleteByName(Config::PS_FACEBOOK_CAPI_TEST_EVENT_CODE);
        }

        if (!$success) {
            http_response_code(400);
        }
        $this->ajaxDie(json_encode(['success' => $success]));
    }

    public function displayAjaxGetProductsWithErrors()
    {
        $page = (int) Tools::getValue('page');
        if (!$page || $page < 0) {
            $page = 0;
        }

        /** @var ProductRepository $productRepository */
        $productRepository = $this->module->getService(ProductRepository::class);
        $productsWithErrors = $productRepository->getProductsWithErrors($this->context->shop->id, $page);

        $this->ajaxDie(json_encode([
            'list' => $productsWithErrors,
            'url' => $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => 1, 'updateproduct' => '1']),
        ]));
    }

    public function displayAjaxUpgradePsAccounts()
    {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();
        $isUpgradeSuccessful = false;
        try {
            /* @phpstan-ignore-next-line */
            $isUpgradeSuccessful = $moduleManager->upgrade('ps_accounts');
        } catch (Exception $e) {
            $errorHandler = ErrorHandler::getInstance();
            $errorHandler->handle(
                new FacebookPsAccountsUpdateException(
                    'Failed to upgrade ps_accounts',
                    FacebookPsAccountsUpdateException::FACEBOOK_PS_ACCOUNTS_UPGRADE_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );

            $this->ajaxDie(json_encode([
                'success' => false,
            ]));
        }

        $this->ajaxDie(json_encode([
            'success' => $isUpgradeSuccessful,
        ]));
    }

    public function displayAjaxGetProductSyncReporting()
    {
        /** @var ProductSyncReportProvider $productSyncReportProvider */
        $productSyncReportProvider = $this->module->getService(ProductSyncReportProvider::class);
        $syncReport = $productSyncReportProvider->getProductSyncReport();

        if (!$syncReport) {
            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                    ]
                )
            );
        }

        $productsWithErrors = isset($syncReport['errors']) ? $syncReport['errors'] : [];
        $lastFinishedSyncStartedAt = isset($syncReport['lastFinishedSyncStartedAt']) ? $syncReport['lastFinishedSyncStartedAt'] : 0;

        /** @var EventBusProductHandler $eventBusProductHandler */
        $eventBusProductHandler = $this->module->getService(EventBusProductHandler::class);

        $shopId = Context::getContext()->shop->id;
        $isoCode = Context::getContext()->language->iso_code;
        $informationAboutProductsWithErrors = $eventBusProductHandler->getInformationAboutEventBusProductsWithErrors($productsWithErrors, $shopId, $isoCode);

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
                    'productsWithErrors' => $informationAboutProductsWithErrors,
                    'lastFinishedSyncStartedAt' => $lastFinishedSyncStartedAt,
                ]
            )
        );
    }

    public function displayAjaxGetProductStatuses()
    {
        /** @var ProductSyncReportProvider $productSyncReportProvider */
        $productSyncReportProvider = $this->module->getService(ProductSyncReportProvider::class);
        $syncReport = $productSyncReportProvider->getProductSyncReport();

        if (!$syncReport) {
            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                    ]
                )
            );
        }

        $productsWithErrors = isset($syncReport['errors']) ? $syncReport['errors'] : [];
        $lastFinishedSyncStartedAt = isset($syncReport['lastFinishedSyncStartedAt']) ? $syncReport['lastFinishedSyncStartedAt'] : 0;

        $page = Tools::getValue('page');
        $status = Tools::getValue('status');
        $sortBy = Tools::getValue('sortBy');
        $sortTo = Tools::getValue('sortTo');
        $searchById = Tools::getValue('searchById');
        $searchByName = Tools::getValue('searchByName');
        $searchByMessage = Tools::getValue('searchByMessage');

        /** @var EventBusProductHandler $eventBusProductHandler */
        $eventBusProductHandler = $this->module->getService(EventBusProductHandler::class);

        $shopId = Context::getContext()->shop->id;
        $informationAboutProducts = $eventBusProductHandler->getFilteredInformationAboutEventBusProducts(
            $productsWithErrors,
            $lastFinishedSyncStartedAt,
            $shopId,
            $page,
            $status,
            $sortBy,
            $sortTo,
            $searchById,
            $searchByName,
            $searchByMessage
        );

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
                    'products' => $informationAboutProducts,
                    'lastFinishedSyncStartedAt' => $lastFinishedSyncStartedAt,
                ]
            )
        );
    }

    public function displayAjaxExportWholeCatalog()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $client = PsApiClient::create($this->env->get('PSX_FACEBOOK_API_URL'));
        $response = 200;

        try {
            $response = $client->post(
                '/account/' . $externalBusinessId . '/reset_product_sync'
            )->json();
        } catch (Exception $e) {
            $errorHandler = ErrorHandler::getInstance();
            $errorHandler->handle(
                new FacebookCatalogExportException(
                    'Failed to export the whole catalog',
                    FacebookCatalogExportException::FACEBOOK_WHOLE_CATALOG_EXPORT_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );
            $this->ajaxDie(json_encode([
                'response' => 500,
                'message' => $e->getMessage(),
            ]));
        }

        $this->ajaxDie(json_encode([
            'response' => $response,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        header('Content-Type: application/json');
        parent::ajaxDie($value, $controller, $method);
    }

    /**
     * Get the documentation url depending on the current language
     *
     * @return string path of the doc
     */
    private function getReadme()
    {
        $isoCode = $this->context->language->iso_code;

        if (!file_exists(_PS_ROOT_DIR_ . _MODULE_DIR_ . $this->module->name . '/docs/user_guide_' . $isoCode . '.pdf')) {
            $isoCode = 'en';
        }

        return _MODULE_DIR_ . $this->module->name . '/docs/user_guide_' . $isoCode . '.pdf';
    }
}
