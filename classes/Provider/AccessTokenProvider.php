<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Exception;
use PrestaShop\AccountsAuth\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Exception\AccessTokenException;
use PrestaShop\Module\PrestashopFacebook\Factory\ErrorHandlerFactory;
use PrestaShop\Module\Ps_facebook\Client\PsApiClient;

class AccessTokenProvider
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    public function __construct(ConfigurationAdapter $configurationAdapter, ErrorHandlerFactory $errorHandlerFactory)
    {
        $this->configurationAdapter = $configurationAdapter;
        $this->errorHandler = $errorHandlerFactory->getErrorHandler();
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        // TODO : implement some cache system to get access token
        // https://graph.facebook.com/oauth/access_token?client_secret=b3f469de46ebc1f94f5b8e3e0db09fc4&grant_type=client_credentials&client_id=726899634800479
        return '';
    }

    public function getOrRefreshToken()
    {
        $accessToken = $this->configurationAdapter->get(Config::FB_ACCESS_TOKEN);
        $tokenExpirationDate = $this->configurationAdapter->get(Config::PS_FACEBOOK_ACCESS_TOKEN_EXPIRATION_DATE);
        $currentTimestamp = time();

        if (!$accessToken || !$tokenExpirationDate || ($tokenExpirationDate - $currentTimestamp <= 86400)) {
            return $this->refreshToken();
        }

        return $accessToken;
    }

    /**
     * @return string|false
     */
    public function refreshToken()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $accessToken = $this->configurationAdapter->get(Config::FB_ACCESS_TOKEN);
        $client = PsApiClient::create($_ENV['PSX_FACEBOOK_API_URL']);

        try {
            $response = $client->post(
                '/account/' . $externalBusinessId . '/exchange_tokens',
                [
                    'json' => [
                        'userAccessToken' => $accessToken,
                    ],
                ]
            )->json();
        } catch (Exception $e) {
            $this->errorHandler->handle(
                new AccessTokenException(
                    'Failed to refresh access token',
                    AccessTokenException::ACCESS_TOKEN_REFRESH_EXCEPTION,
                    $e
                ),
                AccessTokenException::ACCESS_TOKEN_REFRESH_EXCEPTION,
                false
            );

            return false;
        }
        if (isset($response['access_token'])) {
            $currentTimestamp = time();
            $tokenExpiresIn = $currentTimestamp + (int) $response['expires_in'];
            $newAccessToken = $response['access_token'];

            $this->configurationAdapter->updateValue(Config::FB_ACCESS_TOKEN, $newAccessToken);
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_ACCESS_TOKEN_EXPIRATION_DATE, $tokenExpiresIn);

            return $newAccessToken;
        }

        return false;
    }
}
