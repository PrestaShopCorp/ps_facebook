<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Configuration;
use Link;
use PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Database\Config;
use PrestaShop\Module\PrestashopFacebook\DTO\ConfigurationData;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

class ConfigurationHandler
{
    /**
     * @var PsAccountsPresenter
     */
    private $accountsPresenter;

    /**
     * @var PsFacebookTranslations
     */
    private $facebookTranslations;

    private $currencyIso;

    private $languageIso;

    private $languageCode;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(
        PsAccountsPresenter $accountsPresenter,
        PsFacebookTranslations $facebookTranslations,
        ConfigurationAdapter $configurationAdapter,
        Link $link,
        $currencyIso,
        $languageIso,
        $languageCode
    ) {
        $this->accountsPresenter = $accountsPresenter;
        $this->facebookTranslations = $facebookTranslations;
        $this->link = $link;
        $this->currencyIso = $currencyIso;
        $this->languageIso = $languageIso;
        $this->languageCode = $languageCode;
        $this->configurationAdapter = $configurationAdapter;
    }

    public function handle($onboardingInputs)
    {
        $this->saveOnboardingConfiguration($onboardingInputs);

        $configurationData = new ConfigurationData();

        $fbDataProvider = new FacebookDataProvider(
            Config::APP_ID, // 808199653047641
            Configuration::get(Config::FB_ACCESS_TOKEN), //EAAKVHIKFB18BAJ3DDZBPcZBxY9UV3st26azZA7KZCQl48lgVdRh2G4IDwOWX7H6tVMg8qE0WzZC29bhJzmUTO9ZAAtsPXmzZA9gu3bjnilBUL8LsLQUPdxZChKa5QPWx82esxE9O9MZCIh6LrLqIDvxH7D3ZCppqZAmBSiFb2om8D4y02JbRX2rLkTc
            'v8.0'
        );

        $pixelActivationUrl = $this->link->getAdminLink(
            'AdminAjaxPsfacebook',
            true,
            [],
            ['action' => 'activatePixel']
        );

        $onboardingSaveUrl = $this->link->getAdminLink(
            'AdminAjaxPsfacebook',
            true,
            [],
            ['action' => 'saveOnboarding']
        );

        $configurationData
            ->setContextPsAccounts($this->accountsPresenter->present())
            ->setContextPsFacebook($fbDataProvider->getContext($onboardingInputs['fbe']))
            ->setPsFacebookExternalBusinessId('0b2f5f57-5190-47e2-8df6-b2f96447ac9f')
            ->setPsAccountsToken('eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwczovL2lkZW50aXR5dG9vbGtpdC5nb29nbGVhcGlzLmNvbS9nb29nbGUuaWRlbnRpdHkuaWRlbnRpdHl0b29sa2l0LnYxLklkZW50aXR5VG9vbGtpdCIsImlhdCI6MTYwMTY0NzM0MywiZXhwIjoxNjAxNjUwOTQzLCJpc3MiOiJmaXJlYmFzZS1hZG1pbnNkay10ZHZ0cUBwcmVzdGFzaG9wLXJlYWR5LWludGVncmF0aW9uLmlhbS5nc2VydmljZWFjY291bnQuY29tIiwic3ViIjoiZmlyZWJhc2UtYWRtaW5zZGstdGR2dHFAcHJlc3Rhc2hvcC1yZWFkeS1pbnRlZ3JhdGlvbi5pYW0uZ3NlcnZpY2VhY2NvdW50LmNvbSIsInVpZCI6InVNaFhlS0hqQVNadjlRR3FIVXRyUmNpZk4yMzIifQ.OhQvEze9zB0z3aBO4qwKwAZmvZYT1FvKWa9XqJfcRU56sxfJR-xpY2C1DyBmiU6IUEghtdTIH44tvH98ke9eAMFHcduBaP-YPAj7n-oikpmmImN8ctQ7exyiXJBVsZ712AF9JNvs7jpf12ByFdJ2F3CZ6eF7GPLmLXsAlxsZY_rauNU4OBWmZvv8d_8qQvgnGsDjo5XRReTVY_oNDRgn9LO5PIf3oPxDPfEgR1EA7RB94BqRLuVN2exgStD1MGYirIwf-PADmFfCtRXWAyMtqJ0z4fXOqQJSs2ZbqVj5LjYInYWL0UMm5CKTQankNN8xUdc45Ies1qFdFY-eeOSKiQ')
            ->setPsFacebookCurrency($this->currencyIso)
            ->setPsFacebookTimezone($this->configurationAdapter->get('PS_TIMEZONE'))
            ->setPsFacebookLocale($this->configurationAdapter->get('PS_LOCALE_LANGUAGE'))
            ->setPsFacebookPixelActivationRoute($pixelActivationUrl)
            ->setPsFacebookFbeOnboardingSaveRoute($onboardingSaveUrl)
            ->setPsFacebookFbeUiUrl('https://facebook.psessentials-integration.net')
            ->setPsFacebookExternalBusinessId($this->configurationAdapter->get('PS_FACEBOOK_EXTERNAL_BUSINESS_ID'))
            ->setTranslations($this->facebookTranslations->getTranslations())
            ->setIsoCode($this->languageIso)
            ->setLanguageCode($this->languageCode);

        return [
            'success' => true,
            'configurations' => $configurationData->jsonSerialize(),
        ];
    }

    private function saveOnboardingConfiguration(array $onboardingParams)
    {
        $this->configurationAdapter->updateValue(Config::FB_ACCESS_TOKEN, $onboardingParams['access_token']);
    }
}
