<?php
/*X
* 2007-2020 PrestaShop.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2020 PrestaShop SA
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookOnboardException;
use PrestaShop\Module\PrestashopFacebook\Handler\CategoryMatchHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\ConfigurationHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\PrestashopFacebook\Manager\FbeFeatureManager;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeFeatureDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\GoogleCategoryProviderInterface;
use PrestaShop\Module\Ps_facebook\Client\PsApiClient;
use PrestaShop\ModuleLibFaq\Faq;

class AdminAjaxPsfacebookController extends ModuleAdminController
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

        /*
         * @var ConfigurationAdapter $configurationAdapter
         */
        $this->configurationAdapter = $this->module->getService(ConfigurationAdapter::class);
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
        $inputs = json_decode(file_get_contents('php://input'), true);
        $onboardingData = $inputs['onboarding'];

        /** @var ConfigurationHandler $configurationHandler */
        $configurationHandler = $this->module->getService(ConfigurationHandler::class);

        $response = $configurationHandler->handle($onboardingData);

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
        $inputs = json_decode(file_get_contents('php://input'), true);

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
            $client = PsApiClient::create($_ENV['PSX_FACEBOOK_API_URL']);
            $response = $client->post(
                '/account/onboard',
                [
                    'json' => [
                        // For now, not used, so this is not the final URL. To fix if webhook controller is needed.
                        'webhookUrl' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                    ],
                ]
            )->json();

            if (!isset($response['externalBusinessId']) && isset($response['message'])) {
                /** @var ErrorHandler $errorHandler */
                $errorHandler = $this->module->getService(ErrorHandler::class);
                $errorHandler->handle(
                    new FacebookOnboardException(
                        $response['message'],
                        FacebookOnboardException::FACEBOOK_RETRIEVE_EXTERNAL_BUSINESS_ID_EXCEPTION
                    ),
                    FacebookOnboardException::FACEBOOK_RETRIEVE_EXTERNAL_BUSINESS_ID_EXCEPTION
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
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $client = PsApiClient::create($_ENV['PSX_FACEBOOK_API_URL']);
        $response = $client->post(
            '/account/' . $externalBusinessId . '/start_product_sync',
            [
                'json' => [],
            ]
        )->json();

        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START, true);

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
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
        try {
            /* todo: change to data from ajax */
            $categoryMatchHandler->updateCategoryMatch($categoryId, $googleCategoryId, $updateChildren);
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
        $googleCategory = $googleCategoryProvider->getGoogleCategory($categoryId);
        // FIXME : for now, this function will call our API to get taxonomy details about a category ID.
        //  The needed feature is totally different : see ticket http://forge.prestashop.com/browse/EMKTG-305

        $this->ajaxDie(
            json_encode($googleCategory)
        // TODO : need this object : example : { matchingProgress: {total: 789, matched: 12} }
        );
    }

    /**
     * @throws PrestaShopException
     */
    public function displayAjaxGetFeatures()
    {
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
        $inputs = json_decode(file_get_contents('php://input'), true);

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
        // TODO !1: complete object :
        $this->ajaxDie(
            json_encode(
                [
                    'exportDone' => (true == $this->configurationAdapter->get(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START)),
                    'matchingDone' => false, // true if a category match has been called once (at least 1 matching done)
                    'matchingProgress' => ['total' => 42, 'matched' => 0],
                    'reporting' => [
                        'total' => 0,
                        'pending' => 0,
                        'approved' => 0,
                        'disapproved' => 0,
                    ],
                ]
            )
        );
    }

    public function displayAjaxGetCategories()
    {
        $categoryId = (int) Tools::getValue('id_category');
        $page = (int) Tools::getValue('page');

        /** @var GoogleCategoryProviderInterface $googleCategoryProvider */
        $googleCategoryProvider = $this->module->getService(GoogleCategoryProviderInterface::class);
        $googleCategories = $googleCategoryProvider->getGoogleCategoryChildren($categoryId, $page);

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
                    'contactUs' => 'https://www.google.com',
                ]
            )
        );
    }

    /******************************
     * DEVELOPMENT & DEBUG ROUTES *
     ******************************/

    public function displayAjaxUpdateConversionApiData()
    {
        $inputs = json_decode(file_get_contents('php://input'), true);
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

        if (!file_exists(_PS_ROOT_DIR_ . _MODULE_DIR_ . $this->module->name . '/docs/readme_' . $isoCode . '.pdf')) {
            $isoCode = 'en';
        }

        return _MODULE_DIR_ . $this->module->name . '/docs/readme_' . $isoCode . '.pdf';
    }
}
