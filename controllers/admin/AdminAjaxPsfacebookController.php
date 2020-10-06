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

use PrestaShop\Module\PrestashopFacebook\DTO\ConfigurationData;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

class AdminAjaxPsfacebookController extends ModuleAdminController
{
    public function postProcess()
    {
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

    public function ajaxProcessConnectToFacebook()
    {
        $configurationData = new ConfigurationData();
        $psAccountPresenter = new PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter($this->module->name);

        $fbDataProvider = new FacebookDataProvider(
            Configuration::get('PS_PIXEL_ID'), // 808199653047641
            'b3f469de46ebc1f94f5b8e3e0db09fc4', // b3f469de46ebc1f94f5b8e3e0db09fc4
            Configuration::get('fbe_access_token') //EAAKVHIKFB18BAJ3DDZBPcZBxY9UV3st26azZA7KZCQl48lgVdRh2G4IDwOWX7H6tVMg8qE0WzZC29bhJzmUTO9ZAAtsPXmzZA9gu3bjnilBUL8LsLQUPdxZChKa5QPWx82esxE9O9MZCIh6LrLqIDvxH7D3ZCppqZAmBSiFb2om8D4y02JbRX2rLkTc
        );
        $configurationData
            ->setContextPsAccounts($psAccountPresenter->present())
            ->setContextPsFacebook();
        Media::addJsDef([
            'contextPsAccounts' => $psAccountPresenter->present(),
            'contextPsFacebook' => [
                /* 'email' => 'him@prestashop.com',
                'facebookBusinessManager' => [
                  'name' => 'La Fanchonette',
                  'email' => 'fanchonette@ps.com',
                  'createdAt' => 1601283877000
                ],
                'pixel' => [
                  'name' => 'La Fanchonette Test Pixel',
                  'id' => '1234567890',
                  'lastActive' => 1601283877000,
                  'activated' => true
                ],
                'page' => [
                  'name' => 'La Fanchonette',
                  'likes' => 42,
                  'logo' => null
                ],
                'ads' => [
                  'name' => 'La Fanchonette',
                  'email' => 'fanchonette@ps.com',
                  'createdAt' => 1601283877000
                ],
                'categoriesMatching' => [
                  'sent': false
                ]
                */
            ], // from MySQL once FB onboarding done
            // TODO this one given by the API POST /account/onboard:
            'psFacebookExternalBusinessId' => '0b2f5f57-5190-47e2-8df6-b2f96447ac9f',
            // TODO this one given by \PrestaShop\AccountsAuth\Service\PsAccountsService->getOrRefreshToken()
            'psAccountsToken' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwczovL2lkZW50aXR5dG9vbGtpdC5nb29nbGVhcGlzLmNvbS9nb29nbGUuaWRlbnRpdHkuaWRlbnRpdHl0b29sa2l0LnYxLklkZW50aXR5VG9vbGtpdCIsImlhdCI6MTYwMTY0NzM0MywiZXhwIjoxNjAxNjUwOTQzLCJpc3MiOiJmaXJlYmFzZS1hZG1pbnNkay10ZHZ0cUBwcmVzdGFzaG9wLXJlYWR5LWludGVncmF0aW9uLmlhbS5nc2VydmljZWFjY291bnQuY29tIiwic3ViIjoiZmlyZWJhc2UtYWRtaW5zZGstdGR2dHFAcHJlc3Rhc2hvcC1yZWFkeS1pbnRlZ3JhdGlvbi5pYW0uZ3NlcnZpY2VhY2NvdW50LmNvbSIsInVpZCI6InVNaFhlS0hqQVNadjlRR3FIVXRyUmNpZk4yMzIifQ.OhQvEze9zB0z3aBO4qwKwAZmvZYT1FvKWa9XqJfcRU56sxfJR-xpY2C1DyBmiU6IUEghtdTIH44tvH98ke9eAMFHcduBaP-YPAj7n-oikpmmImN8ctQ7exyiXJBVsZ712AF9JNvs7jpf12ByFdJ2F3CZ6eF7GPLmLXsAlxsZY_rauNU4OBWmZvv8d_8qQvgnGsDjo5XRReTVY_oNDRgn9LO5PIf3oPxDPfEgR1EA7RB94BqRLuVN2exgStD1MGYirIwf-PADmFfCtRXWAyMtqJ0z4fXOqQJSs2ZbqVj5LjYInYWL0UMm5CKTQankNN8xUdc45Ies1qFdFY-eeOSKiQ',
            'psFacebookCurrency' => null, // TODO from shop (merchant)
            'psFacebookTimezone' => null, // TODO from shop (merchant)
            'psFacebookLocale' => null, // TODO from shop (merchant)
            'psFacebookPixelActivationRoute' => null, // TODO complete ajax route
            'psFacebookFbeOnboardingSaveRoute' => null, // TODO complete ajax route
            'psFacebookFbeUiUrl' => 'https://facebook.psessentials-integration.net', // TODO by default, use the production URL, but can be overridden by integration URL in a .env (cf ps_metrics)
            'translations' => (new PsFacebookTranslations($this->module))->getTranslations(),
            'i18nSettings' => [
                'isoCode' => $this->context->language->iso_code,
                'languageLocale' => $this->context->language->language_code,
            ],
        ]);
    }
}
