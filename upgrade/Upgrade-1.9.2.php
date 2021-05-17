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

/**
 * @param Ps_facebook $module
 *
 * @return bool
 */
function upgrade_module_1_9_2($module)
{
    $moduleManagerBuilder = \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder::getInstance();
    $moduleManager = $moduleManagerBuilder->build();

    try {
        // - Install will run upgrade if already installed
        // - PS Accounts upgrade will install EventBus
        $moduleManager->install('ps_accounts');
    } catch (Exception $e) {
        $module->getService(
            PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler::class
        )->handle(
            new \PrestaShop\Module\PrestashopFacebook\Exception\FacebookInstallerException(
                'Failed to upgrade PS Accounts',
                \PrestaShop\Module\PrestashopFacebook\Exception\FacebookInstallerException::PS_ACCOUNTS_UPGRADE_EXCEPTION,
                $e
            ),
            $e->getCode(),
            false
        );
    }

    return true;
}
