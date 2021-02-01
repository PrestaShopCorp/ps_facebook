<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Exception;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Config\Env;
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
     * @var Env
     */
    private $env;

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

    public function __construct(ConfigurationAdapter $configurationAdapter, Env $env, ErrorHandler $errorHandler)
    {
        $this->configurationAdapter = $configurationAdapter;
        $this->env = $env;
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

        if ((!$this->userAccessToken
                || !$this->systemAccessToken
                || !$tokenExpirationDate
                || ($tokenExpirationDate - $currentTimestamp <= 86400))
            && \Context::getContext()->controller->controller_type !== 'front'
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
        $client = PsApiClient::create($this->env->get('PSX_FACEBOOK_API_URL'));

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
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, '');
            $this->userAccessToken = '';
            $this->errorHandler->handle(
                new AccessTokenException(
                    'Failed to refresh access token',
                    AccessTokenException::ACCESS_TOKEN_REFRESH_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );

            return;
        }

        if (isset($response['longLived']['access_token'])) {
            $tokenExpiresIn = time() + (70 * 365 * 24 * 3600); // never expires
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
