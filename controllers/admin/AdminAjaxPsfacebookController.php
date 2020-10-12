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

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Database\Config;
use PrestaShop\Module\PrestashopFacebook\Handler\ConfigurationHandler;
use PrestaShop\Module\Ps_facebook\Client\PsApiClient;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

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
            default:
                break;
        }

        return parent::postProcess();
    }

    public function ajaxProcessSaveTokenFbeAccount()
    {
        $token = \Tools::getValue('accessToken');
        $response = Configuration::updateValue('fbe_access_token', $token);

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessFacebookWebhooks()
    {
        // TODO: add some checks
        Configuration::updateValue('fbe_pixel_id', \Tools::getValue('pixel_id'));
        Configuration::updateValue('fbe_business_id', \Tools::getValue('business_id'));
        Configuration::updateValue('fbe_business_manager_id', \Tools::getValue('business_manager_id'));
        Configuration::updateValue('fbe_access_token', \Tools::getValue('access_token'));
        Configuration::updateValue('fbe_profiles', \Tools::getValue('profiles'));
        Configuration::updateValue('fbe_pages', \Tools::getValue('pages'));
        Configuration::updateValue('fbe_ad_account_id', \Tools::getValue('ad_account_id'));
        Configuration::updateValue('fbe_catalog_id', \Tools::getValue('catalog_id'));
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
        $psAccountPresenter = new PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter($this->module->name);
        $facebookTranslations = new PsFacebookTranslations($this->module);
        $configurationAdapter = new ConfigurationAdapter();
        $context = Context::getContext();
        $configurationHandler = new ConfigurationHandler(
            $psAccountPresenter,
            $facebookTranslations,
            $configurationAdapter,
            $context->link,
            $context->currency->iso_code,
            $context->language->iso_code,
            $context->language->language_code
        );

        $response = $configurationHandler->handle($inputs['onboarding']);

        $this->ajaxDie(
            json_encode($response)
        );
    }

    public function ajaxProcessActivatePixel(array $input)
    {
        $isPixelActivated = $input['event_status'];
        Configuration::updateValue(Config::PS_PIXEL_ID, $isPixelActivated);

        $this->ajaxDie();
    }

    private function ajaxProcessRetrieveExternalBusinessId()
    {
        $externalBusinessId = Configuration::get('PS_FACEBOOK_EXTERNAL_BUSINESS_ID');
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
            Configuration::updateValue('PS_FACEBOOK_EXTERNAL_BUSINESS_ID', $externalBusinessId);
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
     * {@inheritdoc}
     */
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        header('Content-Type: application/json');
        parent::ajaxDie($value, $controller, $method);
    }
}
