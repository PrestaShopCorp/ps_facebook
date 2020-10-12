<?php

use PHPUnit\Framework\TestCase;
use PrestaShop\AccountsAuth\Presenter\PsAccountsPresenter;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Handler\ConfigurationHandler;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

class FacebookDataProviderTest extends TestCase
{
    /**
     * @dataProvider getContextDataProvider
     */
    public function testGetContext(
        $inputs,
        $psAccountsPresenterMock,
        $psFacebookTranslationsMock,
        $linkMock,
        $currencyIso,
        $isoCode,
        $languageLocale,
        $result
    ) {
        $psAccountsPresenter = $this->getAccountPresenterMock($psAccountsPresenterMock);
        $psFacebookTranslations = $this->getFacebookTranslationsMock($psFacebookTranslationsMock);
        $link = $this->getLinkMock($linkMock);

        $configurationAdapter = new ConfigurationAdapter();
        $configurationHandler = new ConfigurationHandler(
            $psAccountsPresenter,
            $psFacebookTranslations,
            $configurationAdapter,
            $link,
            $currencyIso,
            $isoCode,
            $languageLocale
        );

        $response = $configurationHandler->handle($inputs['onboarding']);

        self::assertEquals($result, $response);
    }

    public function getContextDataProvider()
    {
        return [
            'without fbe' => [
                'input' => [
                    'onboarding' => [
                        'access_token' => getenv('access_token'),
                        'data_access_expiration_time' => '1610276356',
                        'expires_in' => '3644',
                        'state' => 'state',
                        'fbe' => [
                            'error' => 'TypeError: Failed to fetch',
                        ],
                    ],
                ],
                'PsAccountsPresenterMock' => [
                    'function' => 'present',
                    'return' => [
                        'psIs17' => true,
                        'psAccountsInstallLink' => null,
                        'psAccountsEnableLink' => null,
                        'psAccountsIsInstalled' => true,
                        'psAccountsIsEnabled' => true,
                        'onboardingLink' => 'https://accounts.psessentials-integration.net/shop/account/link/https/5b977a19cc2b.ngrok.io/https/5b977a19cc2b.ngrok.io/ps_facebook?bo=%2FfacebookModule%2Fadmin1%2Findex.php%3Fcontroller%3DAdminModules%26token%3D532728209b1bc373f5fadb089a79b71e%26configure%3Dps_facebook&pubKey=-----BEGIN+RSA+PUBLIC+KEY-----%0D%0AMIGJAoGBANDq2hDEfn%2F5q4fGhVefkCUNGP%2Fm3k29Uxtjim6uqnc6Z%2F%2FjWswmnpqK%0D%0AhPMZTK9fQ8OAAc9AmKMD%2Fky%2FwKb1bW2dD6rOohuMIC72BRo1u2l1RUy3Z%2F19GmEC%0D%0Ah9kSzujMkGGxIYi39UsvAcGM35DuwfkY8jCKOATz0FFjWXHQlqtVAgMBAAE%3D%0D%0A-----END+RSA+PUBLIC+KEY-----&next=%2FfacebookModule%2Fadmin1%2Findex.php%3Fcontroller%3DAdminConfigureHmacPsAccounts%26token%3Df4563b6038ecec5b40c94631bef8e215&name=facebookModule&lang=en',
                        'user' => [
                            'email' => 'marius.gudauskis@invertus.eu',
                            'emailIsValidated' => true,
                            'isSuperAdmin' => true,
                        ],
                        'currentShop' => [
                            'id' => '1',
                            'name' => 'facebookModule',
                            'domain' => '5b977a19cc2b.ngrok.io',
                            'domainSsl' => '5b977a19cc2b.ngrok.io',
                            'url' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminModules&configure=ps_facebook&setShopContext=s-1&token=532728209b1bc373f5fadb089a79b71e',
                        ],
                        'shops' => [
                        ],
                        'superAdminEmail' => 'marius.gudauskis@invertus.eu',
                        'ssoResendVerificationEmail' => 'https://prestashop-newsso-staging.appspot.com/account/send-verification-email',
                        'manageAccountLink' => 'https://prestashop-newsso-staging.appspot.com/login?lang=en',
                    ],
                ],
                'psFacebookTranslationsMock' => [
                    'function' => 'getTranslations',
                    'return' => [
                        'en' => [
                            'general' => [
                                'tabs' => [
                                    'configuration' => 'Configure',
                                    'catalog' => 'Export product catalog',
                                    'help' => 'Help',
                                ],
                            ],
                            'configuration' => [
                                'introduction' => [
                                    'getStarted' => 'Get started',
                                    'subTitle' => 'Build your business presence into Facebook community easily and quickly!',
                                    'proPoints' => '- No credit card required - Easy setup - Cancel anytime',
                                    'resume' => 'Make your first steps with PrestaShop Facebook!',
                                    'proPoint1Title' => 'Manage your business',
                                    'proPoint1Description' => 'Control your ad settings and business tools from one place.',
                                    'proPoint2Title' => 'Manage your ad account',
                                    'proPoint2Description' => 'Choose the account where you store ad payment information for your business.',
                                    'proPoint3Title' => 'Understand your traffic',
                                    'proPoint3Description' => 'Use data from Facebook Pixel* to understand actions people take on your website.',
                                    'proPoint4Title' => 'Build and manage inventory',
                                    'proPoint4Description' => 'Manage your product catalog and unlock the power of ads.',
                                    'proPoint5Title' => 'Reach more people',
                                    'proPoint5Description' => 'Help people find your products on Instagram and Facebook.',
                                    'needMoreFeatures' => 'Need more features?',
                                    'seeDetailedPlans' => 'See detailed plans',
                                ],
                                'messages' => [
                                    'success' => 'PrestaShop Facebook is now activated!',
                                    'syncCatalogAdvice' => 'You first need to import your product catalog so you will be able to set up Facebook Shop and Instagram Shopping and also create ad campaigns.',
                                    'syncCatalogButton' => 'Sync product',
                                    'reloadButton' => 'Reload',
                                    'unknownOnboardingError' => 'An unknown error occurred during onboarding process. Please reload and try again.',
                                ],
                                'facebook' => [
                                    'title' => 'Connect your store to Facebook',
                                    'notConnected' => [
                                        'intro' => 'Integrate your shop with Facebook.',
                                        'connectButton' => 'Connect to Facebook',
                                        'description' => 'With Facebook Business Manager, Facebook Page, Facebook Ads account, Facebook Pixel, Instagram Business account and products, you’ll be able to:',
                                        'details' => '- Create catalog - Sync your catalog automatically and in real time - Create Shop on Facebook Page &amp; Instagram Shopping - Add Messenger plugin in your shop - Customize your Facebook Page with call-to-action',
                                    ],
                                    'connected' => [
                                        'description' => 'You authorize this Facebook account to connect to your store:',
                                        'editButton' => 'Edit',
                                        'facebookBusinessManager' => 'Facebook Business Manager',
                                        'facebookBusinessManagerTooltip' => 'Facebook Business Manager',
                                        'facebookPixel' => 'Facebook Pixel',
                                        'facebookPixelTooltip' => 'Facebook Pixel',
                                        'facebookPage' => 'Facebook Page',
                                        'facebookPageTooltip' => 'Facebook Page',
                                        'facebookAds' => 'Facebook Ads',
                                        'facebookAdsTooltip' => 'Facebook Ads',
                                    ],
                                ],
                                'app' => [
                                    'like' => 'like',
                                    'likes' => 'likes',
                                    'createdAt' => 'Created',
                                    'lastActive' => 'Last active',
                                    'activated' => 'Activated',
                                    'disabled' => 'Disabled',
                                ],
                            ],
                        ],
                    ],
                ],
                'linkMock' => [
                    0 => [
                        'function' => 'getAdminLink',
                        'return' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminAjaxPsfacebook&action=activatePixel&token=8e0378da708f02c24d79273fe6f50fe0',
                    ],
                    1 => [
                        'function' => 'getAdminLink',
                        'return' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminAjaxPsfacebook&action=saveOnboarding&token=8e0378da708f02c24d79273fe6f50fe0',
                    ],
                ],
                'currencyIso' => 'EUR',
                'isoCode' => 'en',
                'languageLocale' => 'en-us',
                'result' => [
                    'success' => true,
                    'configurations' => [
                        'contextPsAccounts' => [
                            'psIs17' => true,
                            'psAccountsInstallLink' => null,
                            'psAccountsEnableLink' => null,
                            'psAccountsIsInstalled' => true,
                            'psAccountsIsEnabled' => true,
                            'onboardingLink' => 'https://accounts.psessentials-integration.net/shop/account/link/https/5b977a19cc2b.ngrok.io/https/5b977a19cc2b.ngrok.io/ps_facebook?bo=%2FfacebookModule%2Fadmin1%2Findex.php%3Fcontroller%3DAdminModules%26token%3D532728209b1bc373f5fadb089a79b71e%26configure%3Dps_facebook&pubKey=-----BEGIN+RSA+PUBLIC+KEY-----%0D%0AMIGJAoGBANDq2hDEfn%2F5q4fGhVefkCUNGP%2Fm3k29Uxtjim6uqnc6Z%2F%2FjWswmnpqK%0D%0AhPMZTK9fQ8OAAc9AmKMD%2Fky%2FwKb1bW2dD6rOohuMIC72BRo1u2l1RUy3Z%2F19GmEC%0D%0Ah9kSzujMkGGxIYi39UsvAcGM35DuwfkY8jCKOATz0FFjWXHQlqtVAgMBAAE%3D%0D%0A-----END+RSA+PUBLIC+KEY-----&next=%2FfacebookModule%2Fadmin1%2Findex.php%3Fcontroller%3DAdminConfigureHmacPsAccounts%26token%3Df4563b6038ecec5b40c94631bef8e215&name=facebookModule&lang=en',
                            'user' => [
                                'email' => 'marius.gudauskis@invertus.eu',
                                'emailIsValidated' => true,
                                'isSuperAdmin' => true,
                            ],
                            'currentShop' => [
                                'id' => '1',
                                'name' => 'facebookModule',
                                'domain' => '5b977a19cc2b.ngrok.io',
                                'domainSsl' => '5b977a19cc2b.ngrok.io',
                                'url' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminModules&configure=ps_facebook&setShopContext=s-1&token=532728209b1bc373f5fadb089a79b71e',
                            ],
                            'shops' => [
                            ],
                            'superAdminEmail' => 'marius.gudauskis@invertus.eu',
                            'ssoResendVerificationEmail' => 'https://prestashop-newsso-staging.appspot.com/account/send-verification-email',
                            'manageAccountLink' => 'https://prestashop-newsso-staging.appspot.com/login?lang=en',
                        ],
                        'psFacebookExternalBusinessId' => '4db7077f-87ba-439a-bd9b-d1364dabb9ae',
                        'psAccountsToken' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwczovL2lkZW50aXR5dG9vbGtpdC5nb29nbGVhcGlzLmNvbS9nb29nbGUuaWRlbnRpdHkuaWRlbnRpdHl0b29sa2l0LnYxLklkZW50aXR5VG9vbGtpdCIsImlhdCI6MTYwMTY0NzM0MywiZXhwIjoxNjAxNjUwOTQzLCJpc3MiOiJmaXJlYmFzZS1hZG1pbnNkay10ZHZ0cUBwcmVzdGFzaG9wLXJlYWR5LWludGVncmF0aW9uLmlhbS5nc2VydmljZWFjY291bnQuY29tIiwic3ViIjoiZmlyZWJhc2UtYWRtaW5zZGstdGR2dHFAcHJlc3Rhc2hvcC1yZWFkeS1pbnRlZ3JhdGlvbi5pYW0uZ3NlcnZpY2VhY2NvdW50LmNvbSIsInVpZCI6InVNaFhlS0hqQVNadjlRR3FIVXRyUmNpZk4yMzIifQ.OhQvEze9zB0z3aBO4qwKwAZmvZYT1FvKWa9XqJfcRU56sxfJR-xpY2C1DyBmiU6IUEghtdTIH44tvH98ke9eAMFHcduBaP-YPAj7n-oikpmmImN8ctQ7exyiXJBVsZ712AF9JNvs7jpf12ByFdJ2F3CZ6eF7GPLmLXsAlxsZY_rauNU4OBWmZvv8d_8qQvgnGsDjo5XRReTVY_oNDRgn9LO5PIf3oPxDPfEgR1EA7RB94BqRLuVN2exgStD1MGYirIwf-PADmFfCtRXWAyMtqJ0z4fXOqQJSs2ZbqVj5LjYInYWL0UMm5CKTQankNN8xUdc45Ies1qFdFY-eeOSKiQ',
                        'psFacebookCurrency' => 'EUR',
                        'psFacebookTimezone' => 'Europe/Riga',
                        'psFacebookLocale' => 'en',
                        'psFacebookPixelActivationRoute' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminAjaxPsfacebook&action=activatePixel&token=8e0378da708f02c24d79273fe6f50fe0',
                        'psFacebookFbeOnboardingSaveRoute' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminAjaxPsfacebook&action=saveOnboarding&token=8e0378da708f02c24d79273fe6f50fe0',
                        'psFacebookFbeUiUrl' => 'https://facebook.psessentials-integration.net',
                        'translations' => [
                            'en' => [
                                'general' => [
                                    'tabs' => [
                                        'configuration' => 'Configure',
                                        'catalog' => 'Export product catalog',
                                        'help' => 'Help',
                                    ],
                                ],
                                'configuration' => [
                                    'introduction' => [
                                        'getStarted' => 'Get started',
                                        'subTitle' => 'Build your business presence into Facebook community easily and quickly!',
                                        'proPoints' => '- No credit card required - Easy setup - Cancel anytime',
                                        'resume' => 'Make your first steps with PrestaShop Facebook!',
                                        'proPoint1Title' => 'Manage your business',
                                        'proPoint1Description' => 'Control your ad settings and business tools from one place.',
                                        'proPoint2Title' => 'Manage your ad account',
                                        'proPoint2Description' => 'Choose the account where you store ad payment information for your business.',
                                        'proPoint3Title' => 'Understand your traffic',
                                        'proPoint3Description' => 'Use data from Facebook Pixel* to understand actions people take on your website.',
                                        'proPoint4Title' => 'Build and manage inventory',
                                        'proPoint4Description' => 'Manage your product catalog and unlock the power of ads.',
                                        'proPoint5Title' => 'Reach more people',
                                        'proPoint5Description' => 'Help people find your products on Instagram and Facebook.',
                                        'needMoreFeatures' => 'Need more features?',
                                        'seeDetailedPlans' => 'See detailed plans',
                                    ],
                                    'messages' => [
                                        'success' => 'PrestaShop Facebook is now activated!',
                                        'syncCatalogAdvice' => 'You first need to import your product catalog so you will be able to set up Facebook Shop and Instagram Shopping and also create ad campaigns.',
                                        'syncCatalogButton' => 'Sync product',
                                        'reloadButton' => 'Reload',
                                        'unknownOnboardingError' => 'An unknown error occurred during onboarding process. Please reload and try again.',
                                    ],
                                    'facebook' => [
                                        'title' => 'Connect your store to Facebook',
                                        'notConnected' => [
                                            'intro' => 'Integrate your shop with Facebook.',
                                            'connectButton' => 'Connect to Facebook',
                                            'description' => 'With Facebook Business Manager, Facebook Page, Facebook Ads account, Facebook Pixel, Instagram Business account and products, you’ll be able to:',
                                            'details' => '- Create catalog - Sync your catalog automatically and in real time - Create Shop on Facebook Page &amp; Instagram Shopping - Add Messenger plugin in your shop - Customize your Facebook Page with call-to-action',
                                        ],
                                        'connected' => [
                                            'description' => 'You authorize this Facebook account to connect to your store:',
                                            'editButton' => 'Edit',
                                            'facebookBusinessManager' => 'Facebook Business Manager',
                                            'facebookBusinessManagerTooltip' => 'Facebook Business Manager',
                                            'facebookPixel' => 'Facebook Pixel',
                                            'facebookPixelTooltip' => 'Facebook Pixel',
                                            'facebookPage' => 'Facebook Page',
                                            'facebookPageTooltip' => 'Facebook Page',
                                            'facebookAds' => 'Facebook Ads',
                                            'facebookAdsTooltip' => 'Facebook Ads',
                                        ],
                                    ],
                                    'app' => [
                                        'like' => 'like',
                                        'likes' => 'likes',
                                        'createdAt' => 'Created',
                                        'lastActive' => 'Last active',
                                        'activated' => 'Activated',
                                        'disabled' => 'Disabled',
                                    ],
                                ],
                            ],
                        ],
                        'i18nSettings' => [
                            'isoCode' => 'en',
                            'languageLocale' => 'en-us',
                        ],
                        'contextPsFacebook' => null,
                    ],
                ],
            ],
            'with fbe' => [
                'input' => [
                    'onboarding' => [
                        'access_token' => getenv('access_token'),
                        'data_access_expiration_time' => '1610276356',
                        'expires_in' => '3644',
                        'state' => 'state',
                        'fbe' => [
                            'pixel' => [
                                'id' => 808199653047641,
                            ],
                            'social media' => [
                                'id' => 3531631736883040,
                            ],
                        ],
                    ],
                ],
                'PsAccountsPresenterMock' => [
                    'function' => 'present',
                    'return' => [
                        'psIs17' => true,
                        'psAccountsInstallLink' => null,
                        'psAccountsEnableLink' => null,
                        'psAccountsIsInstalled' => true,
                        'psAccountsIsEnabled' => true,
                        'onboardingLink' => 'https://accounts.psessentials-integration.net/shop/account/link/https/5b977a19cc2b.ngrok.io/https/5b977a19cc2b.ngrok.io/ps_facebook?bo=%2FfacebookModule%2Fadmin1%2Findex.php%3Fcontroller%3DAdminModules%26token%3D532728209b1bc373f5fadb089a79b71e%26configure%3Dps_facebook&pubKey=-----BEGIN+RSA+PUBLIC+KEY-----%0D%0AMIGJAoGBANDq2hDEfn%2F5q4fGhVefkCUNGP%2Fm3k29Uxtjim6uqnc6Z%2F%2FjWswmnpqK%0D%0AhPMZTK9fQ8OAAc9AmKMD%2Fky%2FwKb1bW2dD6rOohuMIC72BRo1u2l1RUy3Z%2F19GmEC%0D%0Ah9kSzujMkGGxIYi39UsvAcGM35DuwfkY8jCKOATz0FFjWXHQlqtVAgMBAAE%3D%0D%0A-----END+RSA+PUBLIC+KEY-----&next=%2FfacebookModule%2Fadmin1%2Findex.php%3Fcontroller%3DAdminConfigureHmacPsAccounts%26token%3Df4563b6038ecec5b40c94631bef8e215&name=facebookModule&lang=en',
                        'user' => [
                            'email' => 'marius.gudauskis@invertus.eu',
                            'emailIsValidated' => true,
                            'isSuperAdmin' => true,
                        ],
                        'currentShop' => [
                            'id' => '1',
                            'name' => 'facebookModule',
                            'domain' => '5b977a19cc2b.ngrok.io',
                            'domainSsl' => '5b977a19cc2b.ngrok.io',
                            'url' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminModules&configure=ps_facebook&setShopContext=s-1&token=532728209b1bc373f5fadb089a79b71e',
                        ],
                        'shops' => [
                        ],
                        'superAdminEmail' => 'marius.gudauskis@invertus.eu',
                        'ssoResendVerificationEmail' => 'https://prestashop-newsso-staging.appspot.com/account/send-verification-email',
                        'manageAccountLink' => 'https://prestashop-newsso-staging.appspot.com/login?lang=en',
                    ],
                ],
                'psFacebookTranslationsMock' => [
                    'function' => 'getTranslations',
                    'return' => [
                        'en' => [
                            'general' => [
                                'tabs' => [
                                    'configuration' => 'Configure',
                                    'catalog' => 'Export product catalog',
                                    'help' => 'Help',
                                ],
                            ],
                            'configuration' => [
                                'introduction' => [
                                    'getStarted' => 'Get started',
                                    'subTitle' => 'Build your business presence into Facebook community easily and quickly!',
                                    'proPoints' => '- No credit card required - Easy setup - Cancel anytime',
                                    'resume' => 'Make your first steps with PrestaShop Facebook!',
                                    'proPoint1Title' => 'Manage your business',
                                    'proPoint1Description' => 'Control your ad settings and business tools from one place.',
                                    'proPoint2Title' => 'Manage your ad account',
                                    'proPoint2Description' => 'Choose the account where you store ad payment information for your business.',
                                    'proPoint3Title' => 'Understand your traffic',
                                    'proPoint3Description' => 'Use data from Facebook Pixel* to understand actions people take on your website.',
                                    'proPoint4Title' => 'Build and manage inventory',
                                    'proPoint4Description' => 'Manage your product catalog and unlock the power of ads.',
                                    'proPoint5Title' => 'Reach more people',
                                    'proPoint5Description' => 'Help people find your products on Instagram and Facebook.',
                                    'needMoreFeatures' => 'Need more features?',
                                    'seeDetailedPlans' => 'See detailed plans',
                                ],
                                'messages' => [
                                    'success' => 'PrestaShop Facebook is now activated!',
                                    'syncCatalogAdvice' => 'You first need to import your product catalog so you will be able to set up Facebook Shop and Instagram Shopping and also create ad campaigns.',
                                    'syncCatalogButton' => 'Sync product',
                                    'reloadButton' => 'Reload',
                                    'unknownOnboardingError' => 'An unknown error occurred during onboarding process. Please reload and try again.',
                                ],
                                'facebook' => [
                                    'title' => 'Connect your store to Facebook',
                                    'notConnected' => [
                                        'intro' => 'Integrate your shop with Facebook.',
                                        'connectButton' => 'Connect to Facebook',
                                        'description' => 'With Facebook Business Manager, Facebook Page, Facebook Ads account, Facebook Pixel, Instagram Business account and products, you’ll be able to:',
                                        'details' => '- Create catalog - Sync your catalog automatically and in real time - Create Shop on Facebook Page &amp; Instagram Shopping - Add Messenger plugin in your shop - Customize your Facebook Page with call-to-action',
                                    ],
                                    'connected' => [
                                        'description' => 'You authorize this Facebook account to connect to your store:',
                                        'editButton' => 'Edit',
                                        'facebookBusinessManager' => 'Facebook Business Manager',
                                        'facebookBusinessManagerTooltip' => 'Facebook Business Manager',
                                        'facebookPixel' => 'Facebook Pixel',
                                        'facebookPixelTooltip' => 'Facebook Pixel',
                                        'facebookPage' => 'Facebook Page',
                                        'facebookPageTooltip' => 'Facebook Page',
                                        'facebookAds' => 'Facebook Ads',
                                        'facebookAdsTooltip' => 'Facebook Ads',
                                    ],
                                ],
                                'app' => [
                                    'like' => 'like',
                                    'likes' => 'likes',
                                    'createdAt' => 'Created',
                                    'lastActive' => 'Last active',
                                    'activated' => 'Activated',
                                    'disabled' => 'Disabled',
                                ],
                            ],
                        ],
                    ],
                ],
                'linkMock' => [
                    0 => [
                        'function' => 'getAdminLink',
                        'return' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminAjaxPsfacebook&action=activatePixel&token=8e0378da708f02c24d79273fe6f50fe0',
                    ],
                    1 => [
                        'function' => 'getAdminLink',
                        'return' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminAjaxPsfacebook&action=saveOnboarding&token=8e0378da708f02c24d79273fe6f50fe0',
                    ],
                ],
                'currencyIso' => 'EUR',
                'isoCode' => 'en',
                'languageLocale' => 'en-us',
                'result' => [
                    'success' => true,
                    'configurations' => [
                        'contextPsAccounts' => [
                            'psIs17' => true,
                            'psAccountsInstallLink' => null,
                            'psAccountsEnableLink' => null,
                            'psAccountsIsInstalled' => true,
                            'psAccountsIsEnabled' => true,
                            'onboardingLink' => 'https://accounts.psessentials-integration.net/shop/account/link/https/5b977a19cc2b.ngrok.io/https/5b977a19cc2b.ngrok.io/ps_facebook?bo=%2FfacebookModule%2Fadmin1%2Findex.php%3Fcontroller%3DAdminModules%26token%3D532728209b1bc373f5fadb089a79b71e%26configure%3Dps_facebook&pubKey=-----BEGIN+RSA+PUBLIC+KEY-----%0D%0AMIGJAoGBANDq2hDEfn%2F5q4fGhVefkCUNGP%2Fm3k29Uxtjim6uqnc6Z%2F%2FjWswmnpqK%0D%0AhPMZTK9fQ8OAAc9AmKMD%2Fky%2FwKb1bW2dD6rOohuMIC72BRo1u2l1RUy3Z%2F19GmEC%0D%0Ah9kSzujMkGGxIYi39UsvAcGM35DuwfkY8jCKOATz0FFjWXHQlqtVAgMBAAE%3D%0D%0A-----END+RSA+PUBLIC+KEY-----&next=%2FfacebookModule%2Fadmin1%2Findex.php%3Fcontroller%3DAdminConfigureHmacPsAccounts%26token%3Df4563b6038ecec5b40c94631bef8e215&name=facebookModule&lang=en',
                            'user' => [
                                'email' => 'marius.gudauskis@invertus.eu',
                                'emailIsValidated' => true,
                                'isSuperAdmin' => true,
                            ],
                            'currentShop' => [
                                'id' => '1',
                                'name' => 'facebookModule',
                                'domain' => '5b977a19cc2b.ngrok.io',
                                'domainSsl' => '5b977a19cc2b.ngrok.io',
                                'url' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminModules&configure=ps_facebook&setShopContext=s-1&token=532728209b1bc373f5fadb089a79b71e',
                            ],
                            'shops' => [
                            ],
                            'superAdminEmail' => 'marius.gudauskis@invertus.eu',
                            'ssoResendVerificationEmail' => 'https://prestashop-newsso-staging.appspot.com/account/send-verification-email',
                            'manageAccountLink' => 'https://prestashop-newsso-staging.appspot.com/login?lang=en',
                        ],
                        'psFacebookExternalBusinessId' => '4db7077f-87ba-439a-bd9b-d1364dabb9ae',
                        'psAccountsToken' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJodHRwczovL2lkZW50aXR5dG9vbGtpdC5nb29nbGVhcGlzLmNvbS9nb29nbGUuaWRlbnRpdHkuaWRlbnRpdHl0b29sa2l0LnYxLklkZW50aXR5VG9vbGtpdCIsImlhdCI6MTYwMTY0NzM0MywiZXhwIjoxNjAxNjUwOTQzLCJpc3MiOiJmaXJlYmFzZS1hZG1pbnNkay10ZHZ0cUBwcmVzdGFzaG9wLXJlYWR5LWludGVncmF0aW9uLmlhbS5nc2VydmljZWFjY291bnQuY29tIiwic3ViIjoiZmlyZWJhc2UtYWRtaW5zZGstdGR2dHFAcHJlc3Rhc2hvcC1yZWFkeS1pbnRlZ3JhdGlvbi5pYW0uZ3NlcnZpY2VhY2NvdW50LmNvbSIsInVpZCI6InVNaFhlS0hqQVNadjlRR3FIVXRyUmNpZk4yMzIifQ.OhQvEze9zB0z3aBO4qwKwAZmvZYT1FvKWa9XqJfcRU56sxfJR-xpY2C1DyBmiU6IUEghtdTIH44tvH98ke9eAMFHcduBaP-YPAj7n-oikpmmImN8ctQ7exyiXJBVsZ712AF9JNvs7jpf12ByFdJ2F3CZ6eF7GPLmLXsAlxsZY_rauNU4OBWmZvv8d_8qQvgnGsDjo5XRReTVY_oNDRgn9LO5PIf3oPxDPfEgR1EA7RB94BqRLuVN2exgStD1MGYirIwf-PADmFfCtRXWAyMtqJ0z4fXOqQJSs2ZbqVj5LjYInYWL0UMm5CKTQankNN8xUdc45Ies1qFdFY-eeOSKiQ',
                        'psFacebookCurrency' => 'EUR',
                        'psFacebookTimezone' => 'Europe/Riga',
                        'psFacebookLocale' => 'en',
                        'psFacebookPixelActivationRoute' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminAjaxPsfacebook&action=activatePixel&token=8e0378da708f02c24d79273fe6f50fe0',
                        'psFacebookFbeOnboardingSaveRoute' => 'https://5b977a19cc2b.ngrok.io/facebookModule/admin1/index.php?controller=AdminAjaxPsfacebook&action=saveOnboarding&token=8e0378da708f02c24d79273fe6f50fe0',
                        'psFacebookFbeUiUrl' => 'https://facebook.psessentials-integration.net',
                        'translations' => [
                            'en' => [
                                'general' => [
                                    'tabs' => [
                                        'configuration' => 'Configure',
                                        'catalog' => 'Export product catalog',
                                        'help' => 'Help',
                                    ],
                                ],
                                'configuration' => [
                                    'introduction' => [
                                        'getStarted' => 'Get started',
                                        'subTitle' => 'Build your business presence into Facebook community easily and quickly!',
                                        'proPoints' => '- No credit card required - Easy setup - Cancel anytime',
                                        'resume' => 'Make your first steps with PrestaShop Facebook!',
                                        'proPoint1Title' => 'Manage your business',
                                        'proPoint1Description' => 'Control your ad settings and business tools from one place.',
                                        'proPoint2Title' => 'Manage your ad account',
                                        'proPoint2Description' => 'Choose the account where you store ad payment information for your business.',
                                        'proPoint3Title' => 'Understand your traffic',
                                        'proPoint3Description' => 'Use data from Facebook Pixel* to understand actions people take on your website.',
                                        'proPoint4Title' => 'Build and manage inventory',
                                        'proPoint4Description' => 'Manage your product catalog and unlock the power of ads.',
                                        'proPoint5Title' => 'Reach more people',
                                        'proPoint5Description' => 'Help people find your products on Instagram and Facebook.',
                                        'needMoreFeatures' => 'Need more features?',
                                        'seeDetailedPlans' => 'See detailed plans',
                                    ],
                                    'messages' => [
                                        'success' => 'PrestaShop Facebook is now activated!',
                                        'syncCatalogAdvice' => 'You first need to import your product catalog so you will be able to set up Facebook Shop and Instagram Shopping and also create ad campaigns.',
                                        'syncCatalogButton' => 'Sync product',
                                        'reloadButton' => 'Reload',
                                        'unknownOnboardingError' => 'An unknown error occurred during onboarding process. Please reload and try again.',
                                    ],
                                    'facebook' => [
                                        'title' => 'Connect your store to Facebook',
                                        'notConnected' => [
                                            'intro' => 'Integrate your shop with Facebook.',
                                            'connectButton' => 'Connect to Facebook',
                                            'description' => 'With Facebook Business Manager, Facebook Page, Facebook Ads account, Facebook Pixel, Instagram Business account and products, you’ll be able to:',
                                            'details' => '- Create catalog - Sync your catalog automatically and in real time - Create Shop on Facebook Page &amp; Instagram Shopping - Add Messenger plugin in your shop - Customize your Facebook Page with call-to-action',
                                        ],
                                        'connected' => [
                                            'description' => 'You authorize this Facebook account to connect to your store:',
                                            'editButton' => 'Edit',
                                            'facebookBusinessManager' => 'Facebook Business Manager',
                                            'facebookBusinessManagerTooltip' => 'Facebook Business Manager',
                                            'facebookPixel' => 'Facebook Pixel',
                                            'facebookPixelTooltip' => 'Facebook Pixel',
                                            'facebookPage' => 'Facebook Page',
                                            'facebookPageTooltip' => 'Facebook Page',
                                            'facebookAds' => 'Facebook Ads',
                                            'facebookAdsTooltip' => 'Facebook Ads',
                                        ],
                                    ],
                                    'app' => [
                                        'like' => 'like',
                                        'likes' => 'likes',
                                        'createdAt' => 'Created',
                                        'lastActive' => 'Last active',
                                        'activated' => 'Activated',
                                        'disabled' => 'Disabled',
                                    ],
                                ],
                            ],
                        ],
                        'i18nSettings' => [
                            'isoCode' => 'en',
                            'languageLocale' => 'en-us',
                        ],
                        'contextPsFacebook' => [
                            0 => [
                                'id' => 808199653047641,
                                'statusCode' => 400,
                                'errorMessage' => 'Client error response [url] https://graph.facebook.com/v8.0/808199653047641?access_token=EAAKVHIKFB18BAFxOQUZAIljsU3PO0fbiUAwpxhUSlIOKAOGS4obloLMts5yxwKo3gGhO2PPP62MnjwSy4iOoQhqhiu2ZCR7AndPlpJUCR8gkfcoMDNE2FtlErkpbnJDUNws1s0b3ruQznFAuJUkgJzRZBCIcZCmsQOQ1xyS500Tp9CM9QEVxQWSQZB6c65NCuBDCLp1EaQ1tepL1ewej8 [status code] 400 [reason phrase] Bad Request',
                            ],
                            1 => [
                                'category' => 'Business',
                                'link' => 'https://www.facebook.com/games/?app_id=3531631736883040',
                                'name' => 'PrestaShop Social Media - Dev lh',
                                'id' => '3531631736883040',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getAccountPresenterMock(array $psAccountsPresenterMock)
    {
        $psAccountsPresenter = $this->getMockBuilder(PsAccountsPresenter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $psAccountsPresenter->method($psAccountsPresenterMock['function'])->willReturn($psAccountsPresenterMock['return']);

        return $psAccountsPresenter;
    }

    private function getFacebookTranslationsMock(array $psFacebookTranslationsMock)
    {
        $psFacebookTranslations = $this->getMockBuilder(PsFacebookTranslations::class)
            ->disableOriginalConstructor()
            ->getMock();
        $psFacebookTranslations->method($psFacebookTranslationsMock['function'])->willReturn($psFacebookTranslationsMock['return']);

        return $psFacebookTranslations;
    }

    private function getLinkMock(array $linkMock)
    {
        $link = $this->getMockBuilder(Link::class)
            ->disableOriginalConstructor()
            ->getMock();
        foreach ($linkMock as $key => $mock) {
            $link->expects(self::at($key))->method($mock['function'])->willReturn($mock['return']);
        }

        return $link;
    }
}
