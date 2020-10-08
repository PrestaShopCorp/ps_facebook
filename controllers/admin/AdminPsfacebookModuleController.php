<?php

use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

class AdminPsfacebookModuleController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = false;
    }

    public function initContent()
    {
        $psAccountPresenter = new PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter($this->module->name);

        $this->context->smarty->assign([
            'id_pixel' => pSQL(Configuration::get('PS_PIXEL_ID')),
            'access_token' => pSQL(Configuration::get('PS_FBE_ACCESS_TOKEN')),
            'pathApp' => $this->module->getPathUri() . 'views/js/app.js',
            'fbeApp' => $this->module->getPathUri() . 'views/js/main.js',
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebook'),
            'chunkVendor' => $this->module->getPathUri() . 'views/js/chunk-vendors.js',
        ]);

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
            'psFacebookExternalBusinessId' => '1b2f5f57-5190-47e2-8df6-b2f96447ac9f',
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
        $this->content = $this->context->smarty->fetch($this->module->getLocalPath() . '/views/templates/admin/app.tpl');

        parent::initContent();
    }

    public function postProcess()
    {
        $id_pixel = Tools::getValue('PS_PIXEL_ID');
        if (!empty($id_pixel)) {
            Configuration::updateValue('PS_PIXEL_ID', $id_pixel);
        }

        $access_token = Tools::getValue('PS_FBE_ACCESS_TOKEN');
        if (!empty($access_token)) {
            Configuration::updateValue('PS_FBE_ACCESS_TOKEN', $access_token);
        }
    }
}
