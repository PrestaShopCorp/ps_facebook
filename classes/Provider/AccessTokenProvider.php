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

use Controller;
use GuzzleHttp\Psr7\Request;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\ResponseListener;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Exception\AccessTokenException;
use PrestaShop\Module\PrestashopFacebook\Factory\ApiClientFactoryInterface;

class AccessTokenProvider
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var Controller
     */
    private $controller;

    /**
     * @var ApiClientFactoryInterface
     */
    private $psApiClientFactory;

    /**
     * @var ResponseListener
     */
    private $responseListener;

    /**
     * @var string
     */
    private $userAccessToken;

    /**
     * @var string|null
     */
    private $systemAccessToken;

    public function __construct(
        ConfigurationAdapter $configurationAdapter,
        ResponseListener $responseListener,
        $controller,
        ApiClientFactoryInterface $psApiClientFactory
    ) {
        $this->configurationAdapter = $configurationAdapter;
        $this->responseListener = $responseListener;
        $this->controller = $controller;
        $this->psApiClientFactory = $psApiClientFactory;
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

        if ((!$this->systemAccessToken
                || !$tokenExpirationDate
                || ($tokenExpirationDate - $currentTimestamp <= 86400))
            && isset($this->controller->controller_type)
            && $this->controller->controller_type === 'moduleadmin'
            && $this->userAccessToken
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

        $managerId = $this->configurationAdapter->get(Config::PS_FACEBOOK_BUSINESS_MANAGER_ID);
        if (!$managerId) {
            // Force as null, otherwise it gets a falsy value in the API request
            $managerId = null;
        }

        $response = $this->responseListener->handleResponse(
            $this->psApiClientFactory->createClient()->sendRequest(
                new Request(
                    'POST',
                    '/account/' . $externalBusinessId . '/exchange_tokens',
                    [],
                    json_encode([
                        'userAccessToken' => $accessToken,
                        'businessManagerId' => $managerId,
                    ])
                )
            ),
            [
                'exceptionClass' => AccessTokenException::class,
            ]
        );

        if (!$response->isSuccessful()) {
            return;
        }

        $responseContent = $response->getBody();

        if (isset($responseContent['longLived']['access_token'])) {
            $tokenExpiresIn = time() + (70 * 365 * 24 * 3600); // never expires
            $this->userAccessToken = $responseContent['longLived']['access_token'];

            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN, $this->userAccessToken);
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_USER_ACCESS_TOKEN_EXPIRATION_DATE, $tokenExpiresIn);
        }

        if (isset($responseContent['system']['access_token'])) {
            $this->systemAccessToken = $responseContent['system']['access_token'];
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN, $this->systemAccessToken);
        }
    }

    /**
     * Exchange existing tokens with new ones, then store them in the DB + make them available in this class
     *
     * @return array|null
     */
    public function retrieveTokens()
    {
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);

        $response = $this->responseListener->handleResponse(
            $this->psApiClientFactory->createClient()->sendRequest(
                new Request(
                'GET',
                '/account/' . $externalBusinessId . '/app_tokens'
                )
            ),
            [
                'exceptionClass' => AccessTokenException::class,
            ]
        );

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->getBody();
    }
}
