<?php
/*
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

use GuzzleHttp\Client;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Handler\ConfigurationHandler;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeFeatureDataProvider;
use PrestaShop\Module\Ps_facebook\Client\PsApiClient;

class AdminAjaxPsfacebookController extends ModuleAdminController
{
    public function postProcess()
    {
        /**
         *  \Symfony\Component\HttpFoundation\Request::createFromGlobals();
         *  TODO: We should use symfony component or copy function from it later on
         */
        $action = Tools::getValue('action');
        $inputs = json_decode(file_get_contents('php://input'), true);

        switch ($action) {
            case 'saveOnboarding':
                $this->ajaxProcessConnectToFacebook($inputs);
                break;
            case 'activatePixel':
                $this->ajaxProcessActivatePixel($inputs);
                break;
            case 'retrieveExternalBusinessId':
                $this->ajaxProcessRetrieveExternalBusinessId();
                break;
            case 'activateFeature':
                $this->ajaxProcessActivateFeature($inputs);
                break;
            default:
                break;
        }

        return parent::postProcess();
    }

    public function ajaxProcessSaveTokenFbeAccount()
    {
        $token = \Tools::getValue('accessToken');
        $response = Configuration::updateValue(Config::FB_ACCESS_TOKEN, $token);

        $this->ajaxDie(json_encode($response));
    }

    /**
     * Receive the Facebook access token, store it in DB then regerate app data
     *
     * @param array $inputs
     *
     * @throws PrestaShopException
     */
    public function ajaxProcessConnectToFacebook(array $inputs)
    {
        $onboardingData = $inputs['onboarding'];
        $facebookClient = new FacebookClient(
            $onboardingData['access_token'],
            Config::API_VERSION,
            new Client()
        );
        $fbDataProvider = new FacebookDataProvider($facebookClient);

        $configurationAdapter = new ConfigurationAdapter();
        $configurationHandler = new ConfigurationHandler($configurationAdapter, $fbDataProvider);

        $response = $configurationHandler->handle($onboardingData);

        $this->ajaxDie(
            json_encode($response)
        );
    }

    /**
     * Store in database a boolean for know if customer activate pixel
     */
    public function ajaxProcessActivatePixel(array $inputs)
    {
        if (isset($inputs['event_status'])) {
            $pixelStatus = $inputs['event_status'];
            Configuration::updateValue(Config::PS_FACEBOOK_PIXEL_ENABLED, $pixelStatus);
            $this->ajaxDie(json_encode(['success' => true]));
        }

        http_response_code(400);
        $this->ajaxDie(json_encode(['success' => false]));
    }

    private function ajaxProcessRetrieveExternalBusinessId()
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
    public function displayAjaxConfiguration()
    {
        $facebookClient = new FacebookClient(
            Configuration::get(Config::FB_ACCESS_TOKEN),
            Config::API_VERSION,
            new Client()
        );
        $facebookDataProvider = new FacebookDataProvider($facebookClient);

        $fbeDataProvider = new FbeDataProvider(new ConfigurationAdapter());

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
        $facebookClient = new FacebookClient(
            Configuration::get(Config::FB_ACCESS_TOKEN),
            Config::API_VERSION,
            new Client()
        );
        $facebookDataProvider = new FacebookDataProvider($facebookClient);

        $fbeDataProvider = new FbeDataProvider(new ConfigurationAdapter());

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
    public function displayAjaxGetFeatures()
    {
        $facebookClient = new FacebookClient(
            Configuration::get(Config::FB_ACCESS_TOKEN),
            Config::API_VERSION,
            new Client()
        );

        $fbeFeatureDataProvider = new FbeFeatureDataProvider($facebookClient);

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
     * {@inheritdoc}
     */
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        header('Content-Type: application/json');
        parent::ajaxDie($value, $controller, $method);
    }

    private function ajaxProcessActivateFeature($inputs)
    {
    }
}
