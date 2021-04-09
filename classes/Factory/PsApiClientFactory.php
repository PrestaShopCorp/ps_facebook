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

namespace PrestaShop\Module\PrestashopFacebook\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\Event\SubscriberInterface;
use PrestaShop\Module\PrestashopFacebook\Config\Env;
use PrestaShop\PsAccountsInstaller\Installer\Facade\PsAccounts;

class PsApiClientFactory implements ApiClientFactoryInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var PsAccounts
     */
    private $psAccountsFacade;

    /**
     * @var SubscriberInterface
     */
    private $eventSubscriber;

    public function __construct(Env $env, PsAccounts $psAccountsFacade, SubscriberInterface $eventSubscriber)
    {
        $this->baseUrl = $env->get('PSX_FACEBOOK_API_URL');
        $this->psAccountsFacade = $psAccountsFacade;
        $this->eventSubscriber = $eventSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function createClient()
    {
        $client = new Client([
            'base_url' => $this->baseUrl,
            'defaults' => [
                'timeout' => 10,
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->psAccountsFacade->getPsAccountsService()->getOrRefreshToken(),
                ],
            ],
        ]);
        $emitter = $client->getEmitter();
        $emitter->attach($this->eventSubscriber);

        return $client;
    }
}
