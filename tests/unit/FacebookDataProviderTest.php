<?php

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\DTO\Ads;
use PrestaShop\Module\PrestashopFacebook\DTO\ContextPsFacebook;
use PrestaShop\Module\PrestashopFacebook\DTO\FacebookBusinessManager;
use PrestaShop\Module\PrestashopFacebook\Handler\ConfigurationHandler;

class FacebookDataProviderTest extends TestCase
{
    /**
     * Need to add access_token to unit/.env file
     *
     * @dataProvider getContextDataProvider
     */
    public function testGetContext($inputs, $result)
    {
        $configurationAdapter = new ConfigurationAdapter();
        $configurationHandler = new ConfigurationHandler($configurationAdapter);

        $response = $configurationHandler->handle($inputs['onboarding']);

        self::assertEquals($result, $response);
    }

    public function getContextDataProvider()
    {
        $email = 'marius.gudauskis@invertus.eu';

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
                                'email' => $email,
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
                            'superAdminEmail' => $email,
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
                            'business_manager_id' => '123433395433607',
                            'catalog_id' => '337779857455531',
                            'profiles' => [
                                '102085044995399',
                            ],
                            'pages' => [
                                '102085044995399',
                            ],
                            'install_time' => '1601023201',
                            'pixel_id' => '808199653047641',
                            'onsite_eligible' => false,
                        ],
                    ],
                ],
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
                                'email' => $email,
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
                            'superAdminEmail' => $email,
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
                        'contextPsFacebook' => new ContextPsFacebook(
                            $email,
                            new FacebookBusinessManager('PrestaShop', '', 0),
                            null,
                            null,
                            new Ads('PrestaShop', '', 0),
                            false
                        ),
                    ],
                ],
            ],
        ];
    }
}
