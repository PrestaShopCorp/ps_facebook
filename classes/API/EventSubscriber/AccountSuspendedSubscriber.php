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

namespace PrestaShop\Module\PrestashopFacebook\API\EventSubscriber;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\ParsedResponse;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class AccountSuspendedSubscriber implements SubscriberInterface
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct(ConfigurationAdapter $configurationAdapter)
    {
        $this->configurationAdapter = $configurationAdapter;
    }

    public function onParsedResponse(ParsedResponse $response, array $options): void
    {
        $suspension = $response->getResponse()->getHeader('X-Account-Suspended') ?: $response->getResponse()->getHeader('x-account-suspended');
        if (!empty($suspension)) {
            $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_SUSPENSION_REASON, $suspension);
        }
    }
}
