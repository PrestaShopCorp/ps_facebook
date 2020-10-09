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

use PrestaShop\Module\PrestashopFacebook\Database\Config;
use PrestaShop\Module\PrestashopFacebook\DTO\ConfigurationData;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
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

        switch ($action) {
            case 'onboard':
                $inputs = json_decode(file_get_contents("php://input"), true);
                $this->ajaxProcessConnectToFacebook($inputs);
                break;
            case 'activatePixel':
                $this->ajaxProcessActivatePixel();
                break;
            case 'retrieveExternalBusinessId':
                $this->ajaxProcessRetrieveExternalBusinessId();
                break;
            case 'saveOnboarding':
                $this->ajaxProcessOnboardingSave();
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

    // TODO : when this method is used ? to do what ?
    public function ajaxProcessConnectToFacebook(array $inputs)
    {
        $onboardingParams = $inputs['onboarding'];
        $this->saveOnboardingConfiguration($onboardingParams);
        $configurationData = new ConfigurationData();
        $psAccountPresenter = new PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter($this->module->name);

        $fbDataProvider = new FacebookDataProvider(
            Config::APP_ID, // 808199653047641
            $onboardingParams['access_token'], //EAAKVHIKFB18BAJ3DDZBPcZBxY9UV3st26azZA7KZCQl48lgVdRh2G4IDwOWX7H6tVMg8qE0WzZC29bhJzmUTO9ZAAtsPXmzZA9gu3bjnilBUL8LsLQUPdxZChKa5QPWx82esxE9O9MZCIh6LrLqIDvxH7D3ZCppqZAmBSiFb2om8D4y02JbRX2rLkTc
            'v8.0'
        );

        $pixelActivationUrl = $this->context->link->getAdminLink(
            'AdminAjaxPsfacebook',
            true,
            [],
            ['action' => 'activatePixel']
        );

        $onboardingSaveUrl = $this->context->link->getAdminLink(
            'AdminAjaxPsfacebook',
            true,
            [],
            ['action' => 'saveOnboarding']
        );

        $context = Context::getContext();
        $configurationData
            ->setContextPsAccounts($psAccountPresenter->present())
            ->setContextPsFacebook($fbDataProvider->getContext())
            ->setPsFacebookExternalBusinessId('0b2f5f57-5190-47e2-8df6-b2f96447ac9f')
            ->setPsAccountsToken(Configuration::get('PS_ACCOUNTS_FIREBASE_REFRESH_TOKEN'))
            ->setPsFacebookCurrency($context->currency->iso_code)
            ->setPsFacebookTimezone(Configuration::get('PS_TIMEZONE'))
            ->setPsFacebookLocale(Configuration::get('PS_LOCALE_LANGUAGE'))
            ->setPsFacebookPixelActivationRoute($pixelActivationUrl)
            ->setPsFacebookFbeOnboardingSaveRoute($onboardingSaveUrl)
            ->setPsFacebookFbeUiUrl('https://facebook.psessentials-integration.net')
            ->setPsFacebookExternalBusinessId(Configuration::get('PS_FACEBOOK_EXTERNAL_BUSINESS_ID'))
            ->setTranslations((new PsFacebookTranslations($this->module))->getTranslations())
            ->setIsoCode($context->language->iso_code)
            ->setLanguageCode($context->language->language_code);

        $this->ajaxDie(
            json_encode(
                [
                    'success' => true,
                    'configurations' => $configurationData->jsonSerialize()
                ]
            )
        );
    }

    public function ajaxProcessOnboardingSave()
    {
        $test = 1;
    }

    public function ajaxProcessActivatePixel()
    {
        $test = 1;
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
                        'webhookUrl' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
                    ]
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

    private function saveOnboardingConfiguration(array $onboardingParams)
    {
        Configuration::updateValue('FB_ACCESS_TOKEN', $onboardingParams['access_token']);
    }
}
