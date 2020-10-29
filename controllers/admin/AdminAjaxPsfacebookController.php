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

use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Handler\CategoryMatchHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\ConfigurationHandler;
use PrestaShop\Module\PrestashopFacebook\Manager\FbeFeatureManager;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeFeatureDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\GoogleCategoryProviderInterface;
use PrestaShop\Module\Ps_facebook\Api\Shop\RetrieveFaq;
use PrestaShop\Module\Ps_facebook\Client\PsApiClient;
use PrestaShop\ModuleLibFaq\Faq;

class AdminAjaxPsfacebookController extends ModuleAdminController
{
    /** @var Ps_facebook */
    public $module;

    public function displayAjaxSaveTokenFbeAccount()
    {
        $token = \Tools::getValue('accessToken');
        $response = Configuration::updateValue(Config::FB_ACCESS_TOKEN, $token);

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
        $this->displayAjaxConfiguration();
    }

    /**
     * Store in database a boolean for know if customer activate pixel
     */
    public function displayAjaxActivatePixel()
    {
        $inputs = json_decode(file_get_contents('php://input'), true);

        if (isset($inputs['event_status'])) {
            $pixelStatus = $inputs['event_status'];
            Configuration::updateValue(Config::PS_FACEBOOK_PIXEL_ENABLED, $pixelStatus);
            $this->ajaxDie(json_encode(['success' => true]));
        }

        http_response_code(400);
        $this->ajaxDie(json_encode(['success' => false]));
    }

    public function displayAjaxRetrieveExternalBusinessId()
    {
        $externalBusinessId = Configuration::get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
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

            $externalBusinessId = $response['externalBusinessId'];
            Configuration::updateValue(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID, $externalBusinessId);
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
        $externalBusinessId = Configuration::get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $client = PsApiClient::create($_ENV['PSX_FACEBOOK_API_URL']);
        $response = $client->post(
            '/account/' . $externalBusinessId . '/start_product_sync',
            [
                'json' => [],
            ]
        )->json();

        Configuration::updateValue(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START, true);

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
    public function displayAjaxConfiguration()
    {
        /** @var FbeDataProvider $fbeDataProvider */
        $fbeDataProvider = $this->module->getService(FbeDataProvider::class);

        /** @var FacebookDataProvider $facebookDataProvider */
        $facebookDataProvider = $this->module->getService(FacebookDataProvider::class);

        $facebookContext = $facebookDataProvider->getContext($fbeDataProvider->getFbeData());

        $this->ajaxDie(
            json_encode(
                [
                    'psFacebookExternalBusinessId' => Configuration::get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID),
                    'contextPsFacebook' => $facebookContext,
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
                    'psFacebookExternalBusinessId' => Configuration::get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID),
                    'contextPsFacebook' => $facebookContext,
                ]
            )
        );
    }

    public function displayAjaxUpdateCategoryMatch()
    {
        /** @var CategoryMatchHandler $categoryMatchHandler */
        $categoryMatchHandler = $this->module->getService(CategoryMatchHandler::class);

        try {
            /* todo: change to data from ajax */
            $categoryMatchHandler->updateCategoryMatch(3, 8, true);
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
                    'exportDone' => (true == \Configuration::get(Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START)),
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

    /**
     * Retrieve the faq
     *
     * @return array|bool faq or false if no faq associated to the module
     */
    public function displayAjaxRetrieveFaq()
    {
        $faq = new RetrieveFaq();
        // TO DO : remove when we get the module key
        $faq->setModuleKey('697657ffe038d20741105e95a10b12d1');
        // $faq->setModuleKey($this->module->module_key);
        $faq->setPsVersion(_PS_VERSION_);
        $faq->setIsoCode($this->context->language->iso_code);
        $response = $faq->getFaq();

        if (200 !== $response['httpCode']) {
            return false;
        }

        // If no response in the selected language, retrieve the faq in the default language (english)
        if (false === $response['body'] && $faq->getIsoCode() !== 'en') {
            $faq->setIsoCode('en');
            $response = $faq->getFaq();
        }

        $this->ajaxDie(
            json_encode(
                [
                    'faq' => $response['body'],
                ]
            )
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
}
