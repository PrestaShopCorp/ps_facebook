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

use GuzzleHttp\Psr7\Request;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\Client\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\API\ResponseListener;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookCatalogExportException;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookDependencyUpdateException;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookOnboardException;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookPrevalidationScanException;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookProductSyncException;
use PrestaShop\Module\PrestashopFacebook\Factory\PsApiClientFactory;
use PrestaShop\Module\PrestashopFacebook\Handler\CategoryMatchHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\ConfigurationHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\EventBusProductHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\PrevalidationScanRefreshHandler;
use PrestaShop\Module\PrestashopFacebook\Manager\FbeFeatureManager;
use PrestaShop\Module\PrestashopFacebook\Provider\AccessTokenProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeFeatureDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\GoogleCategoryProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\GoogleCategoryProviderInterface;
use PrestaShop\Module\PrestashopFacebook\Provider\PrevalidationScanDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\ProductSyncReportProvider;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;
use PrestaShop\Module\PrestashopFacebook\Repository\ModuleRepository;
use PrestaShop\ModuleLibFaq\Faq;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class AdminAjaxPsfacebookController extends ModuleAdminController
{
    /**
     * @var Ps_facebook
     */
    public $module;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var PsApiClientFactory
     */
    private $clientFactory;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    /**
     * @var ResponseListener
     */
    private $responseListener;

    public function __construct()
    {
        parent::__construct();
        $this->configurationAdapter = $this->module->getService(ConfigurationAdapter::class);
        $this->clientFactory = $this->module->getService(PsApiClientFactory::class);
        $this->errorHandler = $this->module->getService(ErrorHandler::class);
        $this->responseListener = $this->module->getService(ResponseListener::class);
    }

    public function displayAjaxSaveTokenFbeAccount()
    {
        $token = \Tools::getValue('accessToken');
        $response = $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, $token);

        $this->ajaxDie(json_encode($response));
    }

    public function displayAjaxEnsureTokensExchanged()
    {
        /** @var AccessTokenProvider */
        $accessTokenProvider = $this->module->getService(AccessTokenProvider::class);

        $this->ajaxDie(
            json_encode(
                [
                    'success' => (bool) $accessTokenProvider->getSystemAccessToken(),
                ]
            )
        );
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
        /** @var FacebookDataProvider $facebookDataProvider */
        $facebookDataProvider = $this->module->getService(FacebookDataProvider::class);
        /** @var FacebookClient $facebookClient */
        $facebookClient = $this->module->getService(FacebookClient::class);

        $facebookClient->addFbeAttributeIfMissing($onboardingData);
        $configurationHandler->handle($onboardingData);
        $facebookContext = $facebookDataProvider->getContext($onboardingData['fbe']);

        $accessTokenProvider->refreshTokens();
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_FORCED_DISCONNECT, false);
        $this->configurationAdapter->deleteByName(Config::PS_FACEBOOK_SUSPENSION_REASON);

        $this->ajaxDie(
            json_encode([
                'success' => true,
                'contextPsFacebook' => $facebookContext,
            ])
        );
    }

    public function displayAjaxDisconnectFromFacebook()
    {
        /** @var FacebookClient $facebookClient */
        $facebookClient = $this->module->getService(FacebookClient::class);
        // Disconnect from FB
        $facebookClient->uninstallFbe();

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

        try {
            $response = $this->clientFactory->createClient()->sendRequest(
                new Request(
                    'POST',
                    '/account/onboard',
                    [],
                    json_encode([
                        // For now, not used, so this is not the final URL. To fix if webhook controller is needed.
                        'webhookUrl' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                    ])
                )
            );
            $response = json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            $this->errorHandler->handle(
                new FacebookOnboardException(
                    'Failed to onboard on facebook',
                    FacebookOnboardException::FACEBOOK_ONBOARD_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                true
            );
            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'turnOn' => false,
                    ]
                )
            );

            return;
        }

        if (!isset($response['externalBusinessId']) && isset($response['message'])) {
            $this->errorHandler->handle(
                new FacebookOnboardException(
                    json_encode($response['message']),
                    FacebookOnboardException::FACEBOOK_RETRIEVE_EXTERNAL_BUSINESS_ID_EXCEPTION
                )
            );

            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                        'message' => $response['message'],
                        'turnOn' => false,
                    ]
                )
            );

            return;
        }

        if ($response['externalBusinessId'] !== $externalBusinessId) {
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
        try {
            $this->clientFactory->createClient()->sendRequest(
                new Request(
                    'POST',
                    '/account/' . $externalBusinessId . '/start_product_sync',
                    [],
                    json_encode(['turnOn' => $turnOn])
                )
            );
        } catch (Exception $e) {
            $this->errorHandler->handle(
                new FacebookProductSyncException(
                    'Failed to start product sync',
                    FacebookProductSyncException::FACEBOOK_PRODUCT_SYNC,
                    $e
                ),
                $e->getCode(),
                false
            );

            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'turnOn' => false,
                    ]
                )
            );
        }

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
        if ($this->configurationAdapter->get(Config::PS_FACEBOOK_FORCED_DISCONNECT) == true) {
            // $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_FORCED_DISCONNECT, false);
            http_response_code(401);
            $this->ajaxDie(
                json_encode(
                    [
                        'error' => true,
                        'reason' => $this->module->l('We are sorry but the link to Facebook has expired, please reconnect'),
                    ]
            ));
        }
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

        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        $categoryId = isset($inputs['category_id']) ? (int) $inputs['category_id'] : null;
        $googleCategoryId = isset($inputs['google_category_id']) ? (int) $inputs['google_category_id'] : null;
        $googleCategoryName = isset($inputs['google_category_name']) ? $inputs['google_category_name'] : null;
        $googleCategoryParentId = isset($inputs['google_category_parent_id']) ? (int) $inputs['google_category_parent_id'] : null;
        $googleCategoryParentName = isset($inputs['google_category_parent_name']) ? $inputs['google_category_parent_name'] : null;
        $updateChildren = isset($inputs['update_children']) ? (bool) $inputs['update_children'] : false;

        if (!$categoryId) {
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
            $categoryMatchHandler->updateCategoryMatch(
                $categoryId,
                $googleCategoryId,
                $googleCategoryName,
                $googleCategoryParentId,
                $googleCategoryParentName,
                $updateChildren,
                $this->context->shop->id
            );
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

        /**
         * @var FbeFeatureManager
         */
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

    public function displayAjaxDisabledMessengerFeature()
    {
        /**
         * @var FbeFeatureManager
         */
        $featureManager = $this->module->getService(FbeFeatureManager::class);

        $featureManager->updateFeature('messenger_chat', false);

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
                ]
            )
        );
    }

    public function displayAjaxMerchantHasChatDisabled()
    {
        $messengerChatFeature = json_decode($this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . 'messenger_chat'));
        $isEnabled = false;

        if (!empty($messengerChatFeature->enabled)) {
            $isEnabled = $messengerChatFeature->enabled;
        }

        $this->ajaxDie(
            json_encode(
                [
                    'messengerChatStatus' => $isEnabled,
                ]
            )
        );
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxCatalogSummary()
    {
        /** @var PrevalidationScanDataProvider $prevalidationScanDataProvider */
        $prevalidationScanDataProvider = $this->module->getService(PrevalidationScanDataProvider::class);
        /** @var GoogleCategoryRepository $googleCategoryRepository */
        $googleCategoryRepository = $this->module->getService(GoogleCategoryRepository::class);
        /** @var GoogleCategoryProvider $googleCategoryProvider */
        $googleCategoryProvider = $this->module->getService(GoogleCategoryProvider::class);

        $facebookDataProvider = $this->module->getService(FacebookDataProvider::class);
        $informationAboutCategoryMatching = $googleCategoryProvider->getInformationAboutCategoryMatches($this->context->shop->id);
        $productCount = $facebookDataProvider->getProductsInCatalogCount();

        /** @var ProductSyncReportProvider $productSyncReportProvider */
        $productSyncReportProvider = $this->module->getService(ProductSyncReportProvider::class);
        $syncReport = $productSyncReportProvider->getProductSyncReport();

        $this->ajaxDie(
            json_encode(
                [
                    'exportDone' => (true == $this->configurationAdapter->get(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START)),
                    'exportOn' => (true == $this->configurationAdapter->get(Config::PS_FACEBOOK_PRODUCT_SYNC_ON)),
                    'matchingDone' => $googleCategoryRepository->isMatchingDone($this->context->shop->id),
                    'matchingProgress' => $informationAboutCategoryMatching,
                    'validation' => [
                        'prevalidation' => $prevalidationScanDataProvider->getPrevalidationScanSummary($this->context->shop->id),
                        'reporting' => [
                            'lastSyncDate' => $syncReport['lastFinishedSyncStartedAt'],
                            'catalog' => $productCount['product_count'] ?? null,
                            'errored' => count($syncReport['errors']), // no distinction for base lang vs l10n errors
                        ],
                    ],
                ]
            )
        );
    }

    /**
     * Drop cache of pre validation scan then regenerate it.
     * End the method by returning the new summary status.
     */
    public function displayAjaxRunPrevalidationScan()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);
        try {
            /** @var PrevalidationScanRefreshHandler $prevalidationScanRefreshHandler */
            $prevalidationScanRefreshHandler = $this->module->getService(PrevalidationScanRefreshHandler::class);

            return $this->ajaxDie(json_encode(
                $prevalidationScanRefreshHandler->run((int) $inputs['page'] ?: 0)
            ));
        } catch (Exception $e) {
            $this->errorHandler->handle(
                new FacebookPrevalidationScanException(
                    'Failed to run pre validation scan',
                    FacebookPrevalidationScanException::FACEBOOK_PRE_VALIDATION_SCAN_UPDATE_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );

            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
        }
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxCategoryMappingCounters()
    {
        /** @var GoogleCategoryProvider $googleCategoryProvider */
        $googleCategoryProvider = $this->module->getService(GoogleCategoryProvider::class);
        $informationAboutCategoryMatching = $googleCategoryProvider->getInformationAboutCategoryMatches($this->context->shop->id);

        $this->ajaxDie(json_encode(['matchingProgress' => $informationAboutCategoryMatching]));
    }

    public function displayAjaxGetCategories()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        $categoryId = isset($inputs['id_category']) ? (int) $inputs['id_category'] : null;
        $page = isset($inputs['page']) ? (int) $inputs['page'] : null;

        if ($categoryId === null || $page === null) {
            http_response_code(400);
            $this->ajaxDie(
                json_encode(
                    [
                        'success' => false,
                        'message' => 'Missing data',
                    ]
                )
            );
        }

        $shopId = (int) $this->context->shop->id;
        $langId = $this->context->language->id;
        /** @var GoogleCategoryProviderInterface $googleCategoryProvider */
        $googleCategoryProvider = $this->module->getService(GoogleCategoryProviderInterface::class);
        $googleCategories = $googleCategoryProvider->getGoogleCategoryChildren($categoryId, $langId, $shopId, $page);

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
        if ($page < 0) {
            $page = 0;
        }

        /** @var PrevalidationScanDataProvider $prevalidationScanDataProvider */
        $prevalidationScanDataProvider = $this->module->getService(PrevalidationScanDataProvider::class);
        $productsWithErrors = $prevalidationScanDataProvider->getPageOfPrevalidationScan(
            $this->context->shop->id,
            $page
        );

        $this->ajaxDie(json_encode([
            'success' => true,
            'list' => $productsWithErrors,
            'hasMoreProducts' => count($productsWithErrors) === Config::REPORTS_PER_PAGE,
            'url' => $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => 1, 'updateproduct' => '1']),
        ]));
    }

    public function displayAjaxManageModule()
    {
        $moduleName = Tools::getValue('module_name');
        $moduleAction = Tools::getValue('module_action');

        if (!in_array($moduleName, ['ps_accounts', 'ps_eventbus', 'ps_facebook'])
            || !in_array($moduleAction, ['enable', 'install', 'upgrade'])) {
            http_response_code(401);
            $this->ajaxDie(
                json_encode([
                    'success' => false,
                    'message' => 'Module name and/or action are invalid',
                ]));
        }

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        $isActionSuccessful = false;
        try {
            /* @phpstan-ignore-next-line */
            $isActionSuccessful = $moduleManager->{$moduleAction}($moduleName);
        } catch (Exception $e) {
            $this->errorHandler->handle(
                new FacebookDependencyUpdateException(
                    "Failed to $moduleAction $moduleName",
                    FacebookDependencyUpdateException::FACEBOOK_DEPENDENCY_UPGRADE_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );

            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]));
        }

        $this->ajaxDie(json_encode([
            'success' => $isActionSuccessful,
            'message' => $moduleManager->getError($moduleName),
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

        /** @var EventBusProductHandler $eventBusProductHandler */
        $eventBusProductHandler = $this->module->getService(EventBusProductHandler::class);

        $shopId = Context::getContext()->shop->id;
        $isoCode = Context::getContext()->language->iso_code;
        $informationAboutProductsWithErrors = $eventBusProductHandler->getInformationAboutEventBusProductsWithErrors($syncReport['errors'], $shopId, $isoCode);

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
                    'productsWithErrors' => $informationAboutProductsWithErrors,
                    'lastFinishedSyncStartedAt' => $syncReport['lastFinishedSyncStartedAt'],
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

        /** @var EventBusProductHandler $eventBusProductHandler */
        $eventBusProductHandler = $this->module->getService(EventBusProductHandler::class);

        $shopId = Context::getContext()->shop->id;
        $informationAboutProducts = $eventBusProductHandler->getFilteredInformationAboutEventBusProducts(
            $syncReport['errors'],
            $syncReport['lastFinishedSyncStartedAt'],
            $shopId
        );

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
                    'lastFinishedSyncStartedAt' => $syncReport['lastFinishedSyncStartedAt'],
                    'list' => $informationAboutProducts,
                    'url' => $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => 1, 'updateproduct' => '1']),
                ]
            )
        );
    }

    public function displayAjaxExportWholeCatalog()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $response = 200;

        $request = new Request(
            'POST',
            '/account/' . $externalBusinessId . '/reset_product_sync'
        );
        $response = $this->responseListener->handleResponse(
            $this->clientFactory->createClient()->sendRequest($request),
            [
                'exceptionClass' => FacebookCatalogExportException::class,
            ]
        );

        if (!$response->isSuccessful()) {
            $code = $response->getResponse()->getStatusCode();
            $this->ajaxDie(json_encode([
                'response' => 500,
                'message' => "Failed to export the whole catalog (HTTP {$code})",
            ]));
        }

        $this->ajaxDie(json_encode([
            'response' => $response->getBody(),
        ]));
    }

    public function displayAjaxRetrieveTokens()
    {
        /** @var AccessTokenProvider $accessTokenProvider */
        $accessTokenProvider = $this->module->getService(AccessTokenProvider::class);
        $tokens = $accessTokenProvider->retrieveTokens();

        $this->ajaxDie(json_encode([
            'tokens' => $tokens,
            'suspensionReason' => $this->configurationAdapter->get(Config::PS_FACEBOOK_SUSPENSION_REASON),
        ]));
    }

    public function displayAjaxGetModuleStatus()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        if (!isset($inputs['moduleName'])) {
            http_response_code(400);
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => 'Missing moduleName key',
            ]));
        }

        $this->ajaxDie(
            json_encode(
                (new ModuleRepository($inputs['moduleName']))->getInformationsAboutModule()
            )
        );
    }

    public function displayAjaxRegisterHook()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        if (!isset($inputs['hookName'])) {
            http_response_code(400);
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => 'Missing hookName key',
            ]));
        }

        $this->ajaxDie(
            json_encode([
                'success' => $this->module->registerHook($inputs['hookName']),
            ])
        );
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
        $baseUrl = 'https://storage.googleapis.com/psessentials-documentation/' . $this->module->name;

        if (!$this->checkFileExist($baseUrl . '/user_guide_' . $isoCode . '.pdf')) {
            $isoCode = 'en';
        }

        return $baseUrl . '/user_guide_' . $isoCode . '.pdf';
    }

    /**
     * Use cUrl to get HTTP headers and detect any HTTP 404
     *
     * @param string $docUrl
     *
     * @return bool
     */
    private function checkFileExist($docUrl)
    {
        $ch = curl_init($docUrl);

        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $retcode < 400;
    }
}
