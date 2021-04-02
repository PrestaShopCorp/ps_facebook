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

namespace PrestaShop\Module\Ps_facebook\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Message\RequestInterface;
use PrestaShop\AccountsAuth\Service\PsAccountsService;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class PsApiClient extends Client
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * Create the Guzzle Client with defined data
     *
     * @param string $baseUrl
     * @param ConfigurationAdapter $configurationAdapter
     *
     * @return self
     */
    public static function create($baseUrl, ConfigurationAdapter $configurationAdapter)
    {
        $client = new self([
            'base_url' => $baseUrl,
            'defaults' => [
                'timeout' => 10,
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . (new PsAccountsService())->getOrRefreshToken(),
                ],
            ],
        ]);
        $client->setConfigurationAdapter($configurationAdapter);

        return $client;
    }

    public function setConfigurationAdapter(ConfigurationAdapter $configurationAdapter)
    {
        $this->configurationAdapter = $configurationAdapter;
    }

    // TODO : use an event subscriber instead
    public function send(RequestInterface $request)
    {
        $call = parent::send($request);
        $suspension = $call->getHeader('X-Account-Suspended') ?: $call->getHeader('x-account-suspended');
        if (!empty($suspension)) {
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_SUSPENSION_REASON, $suspension);
        }

        return $call;
    }
}
