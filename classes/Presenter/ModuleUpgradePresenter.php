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

namespace PrestaShop\Module\PrestashopFacebook\Presenter;

use Context;
use Module;

class ModuleUpgradePresenter
{
    /**
     * @var Context
     */
    private $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Generate the object responsible of displaying the alert when module upgrade is requested
     *
     * @param string $moduleName
     * @param string $versionRequired
     *
     * @return array
     */
    public function generateModuleDependencyVersionCheck($moduleName, $versionRequired)
    {
        $needsUpgrade = false;
        $currentVersion = null;
        $moduleInstance = null;
        if (Module::isInstalled($moduleName)) {
            $moduleInstance = Module::getInstanceByName($moduleName);
            if ($moduleInstance !== false) {
                $currentVersion = $moduleInstance->version;

                $needsUpgrade = version_compare(
                    $currentVersion,
                    $versionRequired,
                    '<'
                );
            }
        }

        return [
            'needsInstall' => !($moduleInstance && Module::isInstalled($moduleName)),
            'needsEnable' => !Module::isEnabled($moduleName),
            'needsUpgrade' => $needsUpgrade,
            'currentVersion' => $currentVersion,
            'requiredVersion' => $versionRequired,
            'psFacebookUpgradeRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'ManageModule',
                    'module_action' => 'upgrade',
                    'module_name' => $moduleName,
                    'ajax' => 1,
                ]
            ),
            'psFacebookInstallRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'ManageModule',
                    'module_action' => 'install',
                    'module_name' => $moduleName,
                    'ajax' => 1,
                ]
            ),
            'psFacebookEnableRoute' => $this->context->link->getAdminLink(
                'AdminAjaxPsfacebook',
                true,
                [],
                [
                    'action' => 'ManageModule',
                    'module_action' => 'enable',
                    'module_name' => $moduleName,
                    'ajax' => 1,
                ]
            ),
        ];
    }
}
