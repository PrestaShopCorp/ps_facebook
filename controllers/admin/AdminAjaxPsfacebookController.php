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
use PrestaShop\Module\PrestashopFacebook\API\Client\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
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
use PrestaShop\Module\PrestashopFacebook\Http\HttpClient;
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

    public function __construct()
    {
        parent::__construct();
        $this->configurationAdapter = $this->module->getService(ConfigurationAdapter::class);
        $this->clientFactory = $this->module->getService(PsApiClientFactory::class);
        $this->errorHandler = $this->module->getService(ErrorHandler::class);
        $this->ajax = true;
    }

    public function displayAjaxSaveTokenFbeAccount()
    {
        $token = \Tools::getValue('accessToken');
        $response = $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, $token);

        $this->render(json_encode($response), 200);
    }

    public function displayAjaxEnsureTokensExchanged()
    {
        /** @var AccessTokenProvider */
        $accessTokenProvider = $this->module->getService(AccessTokenProvider::class);

        $this->render(
            json_encode(
                [
                    'success' => (bool) $accessTokenProvider->getSystemAccessToken(),
                ]
            ), 200
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

        $this->render(
            json_encode([
                'success' => true,
                'contextPsFacebook' => $facebookContext,
            ]), 200
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
            $this->render(json_encode(['success' => true]), 200);
        }

        $this->render(json_encode(['success' => false]), 400);
    }

    public function displayAjaxRetrieveExternalBusinessId()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);

        try {
            $response = $this->clientFactory->createClient()->post(
                '/account/onboard',
                json_encode([
                    // For now, not used, so this is not the final URL. To fix if webhook controller is needed.
                    'webhookUrl' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                ])
            );
            $response = $response->getBody();
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
            $this->render(
                json_encode(
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'turnOn' => false,
                    ]
                    ), 400
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

            $this->render(
                json_encode(
                    [
                        'success' => false,
                        'message' => $response['message'],
                        'turnOn' => false,
                    ]
                    ), 400
            );

            return;
        }

        if ($response['externalBusinessId'] !== $externalBusinessId) {
            $externalBusinessId = $response['externalBusinessId'];
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID, $externalBusinessId);
        }

        $this->render(
            json_encode(
                [
                    'externalBusinessId' => $externalBusinessId,
                ]
                ), 200
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
            $this->clientFactory->createClient()->post('/account/' . $externalBusinessId . '/start_product_sync', json_encode(['turnOn' => $turnOn]));
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

            $this->render(
                json_encode(
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'turnOn' => false,
                    ]
                    ), 400
            );
        }

        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START, true);
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PRODUCT_SYNC_ON, $turnOn);

        $this->render(
            json_encode(
                [
                    'success' => true,
                    'turnOn' => $turnOn,
                ]
                ), 200
        );
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxGetFbContext()
    {
        if ($this->configurationAdapter->get(Config::PS_FACEBOOK_FORCED_DISCONNECT) == true) {
            // $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_FORCED_DISCONNECT, false);
            $this->render(
                json_encode(
                    [
                        'error' => true,
                        'reason' => $this->module->l('We are sorry but the link to Facebook has expired, please reconnect'),
                    ]
            ), 401);
        }
        /** @var FbeDataProvider $fbeDataProvider */
        $fbeDataProvider = $this->module->getService(FbeDataProvider::class);

        /** @var FacebookDataProvider $facebookDataProvider */
        $facebookDataProvider = $this->module->getService(FacebookDataProvider::class);
        $facebookContext = $facebookDataProvider->getContext($fbeDataProvider->getFbeData());

        $this->render(
            json_encode(
                [
                    'psFacebookExternalBusinessId' => $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID),
                    'contextPsFacebook' => $facebookContext,
                ]
                ), 200
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
            $this->render(
                json_encode(
                    [
                        'success' => false,
                        'message' => 'Missing data',
                    ]
                    ), 200
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
            $this->render(
                json_encode(
                    [
                        'success' => false,
                        'message' => $e->getMessage(),
                    ]
                    ), 500
            );
        }

        $this->render(
            json_encode(
                [
                    'success' => true,
                ]
                ), 200
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

        $this->render(
            json_encode($googleCategory), 200
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

        $this->render(
            json_encode(
                [
                    'fbeFeatures' => $fbeFeatures,
                ]
                ), 200
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
            $this->render(
                json_encode(
                    $response
                ), 200
            );
        } else {
            $this->render(
                json_encode(
                    [
                        'success' => false,
                    ]
                    ), 200
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

        $this->render(
            json_encode(
                [
                    'success' => true,
                ]
                ), 200
        );
    }

    public function displayAjaxMerchantHasChatDisabled()
    {
        $messengerChatFeature = json_decode($this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . 'messenger_chat'));
        $isEnabled = false;

        if (!empty($messengerChatFeature->enabled)) {
            $isEnabled = $messengerChatFeature->enabled;
        }

        $this->render(
            json_encode(
                [
                    'messengerChatStatus' => $isEnabled,
                ]
                ), 200
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

        $this->render(
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
                ),
                200
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

            return $this->render(json_encode(
                $prevalidationScanRefreshHandler->run((int) $inputs['page'] ?: 0)
            ), 200);
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

            $this->render(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]), 400);
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

        $this->render(json_encode(['matchingProgress' => $informationAboutCategoryMatching]), 200);
    }

    public function displayAjaxGetCategories()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        $categoryId = isset($inputs['id_category']) ? (int) $inputs['id_category'] : null;
        $page = isset($inputs['page']) ? (int) $inputs['page'] : null;

        if ($categoryId === null || $page === null) {
            $this->render(
                json_encode(
                    [
                        'success' => false,
                        'message' => 'Missing data',
                    ]
                    ), 400
            );
        }

        $shopId = (int) $this->context->shop->id;
        $langId = $this->context->language->id;
        /** @var GoogleCategoryProviderInterface $googleCategoryProvider */
        $googleCategoryProvider = $this->module->getService(GoogleCategoryProviderInterface::class);
        $googleCategories = $googleCategoryProvider->getGoogleCategoryChildren($categoryId, $langId, $shopId, $page);

        $this->render(
            json_encode($googleCategories), 200
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
        $faq = [
            'categories' => [],
        ];

        $request = new HttpClient('https://api.addons.prestashop.com');
        $result = $request->get('/request/faq/' . $this->module->module_key . '/' . _PS_VERSION_ . '/' . $this->context->language->iso_code, []);

        if ($result->getStatusCode() === 200) {
            $faq['categories'] = $result->getBody();
        }

        $this->render(
            json_encode(
                [
                    'faq' => $faq['categories'],
                    'doc' => $this->getReadme(),
                ]
                ), 200
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

        $this->render(json_encode(['success' => $success]), $success ? 200 : 400);
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

        $this->render(json_encode([
            'success' => true,
            'list' => $productsWithErrors,
            'hasMoreProducts' => count($productsWithErrors) === Config::REPORTS_PER_PAGE,
            'url' => $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => 1, 'updateproduct' => '1']),
        ]), 200);
    }

    public function displayAjaxManageModule()
    {
        $moduleName = Tools::getValue('module_name');
        $moduleAction = Tools::getValue('module_action');

        if (!in_array($moduleName, ['ps_accounts', 'ps_eventbus', 'ps_facebook'])
            || !in_array($moduleAction, ['enable', 'install', 'upgrade'])) {
            $this->render(
                json_encode([
                    'success' => false,
                    'message' => 'Module name and/or action are invalid',
                ]), 401);
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

            $this->render(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]), 200);
        }

        $this->render(json_encode([
            'success' => $isActionSuccessful,
            'message' => $moduleManager->getError($moduleName),
        ]), 200);
    }

    public function displayAjaxGetProductSyncReporting()
    {
        /** @var ProductSyncReportProvider $productSyncReportProvider */
        $productSyncReportProvider = $this->module->getService(ProductSyncReportProvider::class);
        $syncReport = $productSyncReportProvider->getProductSyncReport();

        if (!$syncReport) {
            $this->render(
                json_encode(
                    [
                        'success' => false,
                    ]
                    ), 200
            );
        }

        /** @var EventBusProductHandler $eventBusProductHandler */
        $eventBusProductHandler = $this->module->getService(EventBusProductHandler::class);

        $shopId = Context::getContext()->shop->id;
        $isoCode = Context::getContext()->language->iso_code;
        $informationAboutProductsWithErrors = $eventBusProductHandler->getInformationAboutEventBusProductsWithErrors($syncReport['errors'], $shopId, $isoCode);

        $this->render(
            json_encode(
                [
                    'success' => true,
                    'productsWithErrors' => $informationAboutProductsWithErrors,
                    'lastFinishedSyncStartedAt' => $syncReport['lastFinishedSyncStartedAt'],
                ]
            ),
            200
        );
    }

    public function displayAjaxGetProductStatuses()
    {
        /** @var ProductSyncReportProvider $productSyncReportProvider */
        $productSyncReportProvider = $this->module->getService(ProductSyncReportProvider::class);
        $syncReport = $productSyncReportProvider->getProductSyncReport();

        if (!$syncReport) {
            $this->render(
                json_encode(
                    [
                        'success' => false,
                    ]
                ),
                200
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

        $this->render(
            json_encode(
                [
                    'success' => true,
                    'lastFinishedSyncStartedAt' => $syncReport['lastFinishedSyncStartedAt'],
                    'list' => $informationAboutProducts,
                    'url' => $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => 1, 'updateproduct' => '1']),
                ]
            ),
            200
        );
    }

    public function displayAjaxExportWholeCatalog()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $response = 200;

        $response = $this->clientFactory->createClient()->post('/account/' . $externalBusinessId . '/reset_product_sync');

        if (!$response->isSuccessful()) {
            $code = $response->getStatusCode();
            $this->render(json_encode([
                'response' => 500,
                'message' => "Failed to export the whole catalog (HTTP {$code})",
            ]), 500);
        }

        $this->render(json_encode([
            'response' => $response->getBody(),
        ]), 200);
    }

    public function displayAjaxRetrieveTokens()
    {
        /** @var AccessTokenProvider $accessTokenProvider */
        $accessTokenProvider = $this->module->getService(AccessTokenProvider::class);
        $tokens = $accessTokenProvider->retrieveTokens();

        $this->render(json_encode([
            'tokens' => $tokens,
            'suspensionReason' => $this->configurationAdapter->get(Config::PS_FACEBOOK_SUSPENSION_REASON),
        ]), 200);
    }

    public function displayAjaxGetModuleStatus()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        if (!isset($inputs['moduleName'])) {
            $this->render(json_encode([
                'success' => false,
                'message' => 'Missing moduleName key',
            ]), 400);
        }

        $this->render(
            json_encode(
                (new ModuleRepository($inputs['moduleName']))->getInformationsAboutModule()
            ),
            200
        );
    }

    public function displayAjaxRegisterHook()
    {
        $inputs = json_decode(Tools::file_get_contents('php://input'), true);

        if (!isset($inputs['hookName'])) {
            $this->render(json_encode([
                'success' => false,
                'message' => 'Missing hookName key',
            ]), 400);
        }

        $this->render(
            json_encode([
                'success' => $this->module->registerHook($inputs['hookName']),
            ]),
            200
        );
    }

    /**
     * {@inheritdoc}
     */
    private function render($response, $code)
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/json;charset=utf-8');
        header("HTTP/1.1 $code");

        if ((bool) version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            //@phpstan-ignore-next-line
            parent::ajaxRender($response);
            exit;
        }

        parent::ajaxDie($response, null, null);
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
