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

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Exception;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\PsAccountsInstaller\Installer\Facade\PsAccounts;

class ServerInformationRepository
{
    /**
     * @var bool
     */
    private $isPsAccountsOnboarded;

    public function __construct(PsAccounts $psAccountsFacade)
    {
        try {
            $this->isPsAccountsOnboarded = (bool) $psAccountsFacade->getPsAccountsService()->getOrRefreshToken();
        } catch (Exception $e) {
            $this->isPsAccountsOnboarded = false;
        }
    }

    /**
     * @return array
     */
    public function getHealthCheckData()
    {
        $isFacebookSystemTokenSet = false;
        if (\Configuration::get(Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN)) {
            $isFacebookSystemTokenSet = true;
        }

        return [
            'ps_accounts' => \Module::isInstalled('ps_accounts'),
            'ps_accounts_onboarded' => $this->isPsAccountsOnboarded,
            'ps_eventbus' => \Module::isInstalled('ps_eventbus'),
            'ps_facebook_system_token_set' => $isFacebookSystemTokenSet,
            'pixel_enabled' => (bool) \Configuration::get(Config::PS_FACEBOOK_PIXEL_ENABLED),
            'pixel_id' => (bool) \Configuration::get(Config::PS_PIXEL_ID),
            'profile_id' => (bool) \Configuration::get(Config::PS_FACEBOOK_PROFILES),
            'page_id' => (bool) \Configuration::get(Config::PS_FACEBOOK_PAGES),
            'business_manager_id' => (bool) \Configuration::get(Config::PS_FACEBOOK_BUSINESS_MANAGER_ID),
            'ad_account_id' => (bool) \Configuration::get(Config::PS_FACEBOOK_AD_ACCOUNT_ID),
            'catalog_id' => (bool) \Configuration::get(Config::PS_FACEBOOK_CATALOG_ID),
            'env' => [
                'PSX_FACEBOOK_API_URL' => isset($_ENV['PSX_FACEBOOK_API_URL']) ? $_ENV['PSX_FACEBOOK_API_URL'] : null,
                'ACCOUNTS_SVC_API_URL' => isset($_ENV['ACCOUNTS_SVC_API_URL']) ? $_ENV['ACCOUNTS_SVC_API_URL'] : null,
                'EVENT_BUS_PROXY_API_URL' => isset($_ENV['EVENT_BUS_PROXY_API_URL']) ? $_ENV['EVENT_BUS_PROXY_API_URL'] : null,
                'EVENT_BUS_SYNC_API_URL' => isset($_ENV['EVENT_BUS_SYNC_API_URL']) ? $_ENV['EVENT_BUS_SYNC_API_URL'] : null,
            ],
        ];
    }
}
