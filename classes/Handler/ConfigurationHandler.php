<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Configuration;
use Link;
use PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Database\Config;
use PrestaShop\Module\PrestashopFacebook\DTO\ConfigurationData;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookDataProvider;
use PrestaShop\Module\PrestashopFacebook\Provider\FacebookFbeDataProvider;
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

    /**
     * @var Link
     */
    private $link;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var string
     */
    private $currencyIso;

    /**
     * @var string
     */
    private $languageIso;

    /**
     * @var string
     */
    private $languageCode;

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
        $this->addFbeAttributeIfMissing($onboardingInputs);
        $this->saveOnboardingConfiguration($onboardingInputs);

        $configurationData = new ConfigurationData();

        $fbDataProvider = new FacebookDataProvider(
            Config::APP_ID,
            $this->configurationAdapter->get(Config::FB_ACCESS_TOKEN),
            Config::API_VERSION
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

    private function addFbeAttributeIfMissing(array &$onboardingParams)
    {
        if (!empty($onboardingParams['fbe']) && !isset($onboardingParams['fbe']['error'])) {
            return;
        }

        $onboardingParams['fbe'] = (new FacebookFbeDataProvider(
                Config::APP_ID,
                $onboardingParams['access_token'],
                Config::API_VERSION,
                $this->configurationAdapter->get('PS_FACEBOOK_EXTERNAL_BUSINESS_ID')
            ))->getFbeInstallationContext();
    }

    private function saveOnboardingConfiguration(array $onboardingParams)
    {
        $this->configurationAdapter->updateValue(Config::FB_ACCESS_TOKEN, $onboardingParams['access_token']);
        $this->configurationAdapter->updateValue(Config::PS_PIXEL_ID, isset($onboardingParams['fbe']['pixel_id']) ? $onboardingParams['fbe']['pixel_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PROFILES, isset($onboardingParams['fbe']['profiles']) ? implode(',', $onboardingParams['fbe']['profiles']) : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_PAGES, isset($onboardingParams['fbe']['pages']) ? implode(',', $onboardingParams['fbe']['pages']) : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_BUSINESS_MANAGER_ID, isset($onboardingParams['fbe']['business_manager_id']) ? $onboardingParams['fbe']['business_manager_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_AD_ACCOUNT_ID, isset($onboardingParams['fbe']['ad_account_id']) ? $onboardingParams['fbe']['ad_account_id'] : '');
        $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_CATALOG_ID, isset($onboardingParams['fbe']['catalog_id']) ? $onboardingParams['fbe']['catalog_id'] : '');
    }
}
