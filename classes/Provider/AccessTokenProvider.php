<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Exception;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Exception\AccessTokenException;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
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

    /**
     * @var string
     */
    private $userAccessToken;

    /**
     * @var string|null
     */
    private $systemAccessToken;

    public function __construct(ConfigurationAdapter $configurationAdapter, ErrorHandler $errorHandler)
    {
        $this->configurationAdapter = $configurationAdapter;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @return string
     */
    public function getUserAccessToken()
    {
        if (!$this->userAccessToken) {
            $this->getOrRefreshTokens();
        }

        return $this->userAccessToken;
    }

    /**
     * @return string|null
     */
    public function getSystemAccessToken()
    {
        if (!$this->systemAccessToken) {
            $this->getOrRefreshTokens();
        }

        return $this->systemAccessToken;
    }

    /**
     * Load data from configuration table and request from API them if something is missing
     */
    private function getOrRefreshTokens()
    {
        $this->userAccessToken = $this->configurationAdapter->get(Config::PS_FACEBOOK_USER_ACCESS_TOKEN);
        $this->systemAccessToken = $this->configurationAdapter->get(Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN);
        $tokenExpirationDate = $this->configurationAdapter->get(Config::PS_FACEBOOK_USER_ACCESS_TOKEN_EXPIRATION_DATE);
        $currentTimestamp = time();

        if (!$this->userAccessToken
            || !$this->systemAccessToken
            || !$tokenExpirationDate
            || ($tokenExpirationDate - $currentTimestamp <= 86400)
        ) {
            $this->refreshTokens();
        }
    }

    /**
     * Exchange existing tokens with new ones, then store them in the DB + make them available in this class
     *
     * @return void
     */
    public function refreshTokens()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);
        $accessToken = $this->configurationAdapter->get(Config::PS_FACEBOOK_USER_ACCESS_TOKEN);
        $client = PsApiClient::create($_ENV['PSX_FACEBOOK_API_URL']);

        $managerId = $this->configurationAdapter->get(Config::PS_FACEBOOK_BUSINESS_MANAGER_ID);
        if (!$managerId) {
            // Force as null, otherwise it gets a falsy value in the API request
            $managerId = null;
        }

        try {
            $response = $client->post(
                '/account/' . $externalBusinessId . '/exchange_tokens',
                [
                    'json' => [
                        'userAccessToken' => $accessToken,
                        'businessManagerId' => $managerId,
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

            return;
        }

        if (isset($response['longLived']['access_token'])) {
            $tokenExpiresIn = time() + (int) $response['longLived']['expires_in'];
            $this->userAccessToken = $response['longLived']['access_token'];

            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, $this->userAccessToken);
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN_EXPIRATION_DATE, $tokenExpiresIn);
        }

        if (isset($response['system']['access_token'])) {
            $this->systemAccessToken = $response['system']['access_token'];
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN, $this->systemAccessToken);
        }
    }
}
