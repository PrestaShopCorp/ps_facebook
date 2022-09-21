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

namespace PrestaShop\Module\PrestashopFacebook\Database;

use Exception;
use Language;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookInstallerException;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\Ps_facebook\Tracker\Segment;
use Tab;

class Installer
{
    public const CLASS_NAME = 'Installer';

    public const CONFIGURATION_LIST = [
        Config::PS_PIXEL_ID,
        Config::PS_FACEBOOK_USER_ACCESS_TOKEN,
        Config::PS_FACEBOOK_PROFILES,
        Config::PS_FACEBOOK_PAGES,
        Config::PS_FACEBOOK_BUSINESS_MANAGER_ID,
        Config::PS_FACEBOOK_AD_ACCOUNT_ID,
        Config::PS_FACEBOOK_CATALOG_ID,
        Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID,
        Config::PS_FACEBOOK_PIXEL_ENABLED,
        Config::PS_FACEBOOK_PRODUCT_SYNC_FIRST_START,
        Config::PS_FACEBOOK_PRODUCT_SYNC_ON,
    ];

    private $module;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Segment
     */
    private $segment;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    public function __construct(\Ps_facebook $module, Segment $segment, ErrorHandler $errorHandler)
    {
        $this->module = $module;
        $this->segment = $segment;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @return bool
     */
    public function install()
    {
        $this->segment->setMessage('PS Facebook installed');
        $this->segment->track();

        return $this->installConfiguration() &&
            $this->installTabs() &&
            $this->installTables();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Install configuration for each shop
     *
     * @return bool
     */
    public function installConfiguration()
    {
        $result = true;

        foreach (\Shop::getShops(false, null, true) as $shopId) {
            foreach (self::CONFIGURATION_LIST as $name => $value) {
                if (false === \Configuration::hasKey((string) $name, null, null, (int) $shopId)) {
                    $result = $result && \Configuration::updateValue(
                        (string) $name,
                        $value,
                        false,
                        null,
                        (int) $shopId
                    );
                }
            }
        }

        return $result;
    }

    /**
     * This method is often use to create an ajax controller
     *
     * @return bool
     */
    public function installTabs()
    {
        $installTabCompleted = true;

        foreach ($this->getTabs() as $tab) {
            try {
                $installTabCompleted = $installTabCompleted && $this->installTab(
                    $tab['className'],
                    $tab['parent'],
                    $tab['name'],
                    $tab['module'],
                    $tab['active'],
                    $tab['icon']
                );
            } catch (Exception $e) {
                $this->errorHandler->handle(
                    new FacebookInstallerException(
                        'Failed to install module tabs',
                        FacebookInstallerException::FACEBOOK_INSTALL_EXCEPTION,
                        $e
                    ),
                    FacebookInstallerException::FACEBOOK_INSTALL_EXCEPTION,
                    false
                );
                $this->errors[] = sprintf(
                    $this->module->l('Failed to install %1s tab', self::CLASS_NAME),
                    $tab['className']
                );

                return false;
            }
        }

        return $installTabCompleted;
    }

    public function installTab($className, $parent, $name, $module, $active, $icon)
    {
        if (Tab::getIdFromClassName($className)) {
            return true;
        }

        $idParent = is_int($parent) ? $parent : Tab::getIdFromClassName($parent);

        $moduleTab = new Tab();
        $moduleTab->class_name = $className;
        $moduleTab->id_parent = $idParent;
        $moduleTab->module = $module;
        $moduleTab->active = $active;
        if (property_exists($moduleTab, 'icon')) {
            $moduleTab->icon = $icon;
        }

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $moduleTab->name[$language['id_lang']] = $name;
        }

        return $moduleTab->add();
    }

    public function installTables()
    {
        try {
            include dirname(__FILE__) . '/../../sql/install.php';
        } catch (\Exception $e) {
            $this->errorHandler->handle(
                new FacebookInstallerException(
                    'Failed to install database tables',
                    FacebookInstallerException::FACEBOOK_INSTALL_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );
            $this->errors[] = $this->module->l('Failed to install database tables', self::CLASS_NAME);

            return false;
        }

        return true;
    }

    private function getTabs()
    {
        return [
            [
                'className' => 'Marketing',
                'parent' => 'IMPROVE',
                'name' => 'Marketing',
                'module' => '',
                'active' => true,
                'icon' => 'campaign',
            ],
            [
                'className' => 'AdminPsfacebookModule',
                'parent' => 'Marketing',
                'name' => 'Facebook',
                'module' => $this->module->name,
                'active' => true,
                'icon' => '',
            ],
            [
                'className' => 'AdminAjaxPsfacebook',
                'parent' => -1,
                'name' => $this->module->name,
                'module' => $this->module->name,
                'active' => true,
                'icon' => '',
            ],
        ];
    }
}
