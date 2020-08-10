<?php
/**
 * 2007-2020 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Ps_facebook extends Module
{
    const MODULE_ADMIN_CONTROLLERS = [
        'AdminAjaxPsfacebookController',
    ];

    const HOOK_LIST = [
        'displayHeader'
    ];

    public $name;
    public $tab;
    public $version;
    public $author;
    public $need_instance;
    public $module_key;
    public $controllerAdmin;
    public $bootstrap;
    public $displayName;
    public $description;
    public $psVersionIs17;
    public $css_path;
    public $docs_path;
    public $confirmUninstall;
    public $ps_versions_compliancy;
    public $compiled_path;
    public $js_path;
    public $configurationList = [
        'fbe_account_id'
    ];

    public function __construct()
    {
        $this->name = 'ps_facebook';
        $this->tab = 'advertising_marketing';
        $this->version = '2.0.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        // $this->module_key = '82bc763z4cfef947e06f15c78f5ete2e';

        $this->controllerAdmin = 'AdminAjaxPsfacebook';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ps Facebook');
        $this->description = $this->l('Ps facebook');
        $this->psVersionIs17 = (bool) version_compare(_PS_VERSION_, '1.7', '>=');

        $this->css_path = $this->_path . 'views/css/';
        $this->js_path = $this->_path . 'views/js/';
        $this->docs_path = $this->_path . 'docs/';

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    /**
     * This method is trigger at the installation of the module
     * - install all module tables
     * - set some configuration value
     * - register hook used by the module.
     *
     * @return bool
     */
    public function install()
    {
        return parent::install() &&
            $this->installConfiguration() &&
            $this->registerHook(self::HOOK_LIST) &&
            $this->installTabs();
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
            foreach ($this->configurationList as $name => $value) {
                if (false === Configuration::hasKey($name, null, null, (int) $shopId)) {
                    $result = $result && Configuration::updateValue(
                        $name,
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

        foreach (static::MODULE_ADMIN_CONTROLLERS as $controllerName) {
            if (Tab::getIdFromClassName($controllerName)) {
                continue;
            }

            $tab = new Tab();
            $tab->class_name = $controllerName;
            $tab->active = true;
            $tab->name = array_fill_keys(
                Language::getIDs(false),
                $this->displayName
            );
            $tab->id_parent = -1;
            $tab->module = $this->name;
            $installTabCompleted = $installTabCompleted && $tab->add();
        }

        return $installTabCompleted;
    }

    /**
     * Triggered at the uninstall of the module
     * - erase tables
     * - erase configuration value
     * - unregister hook.
     *
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * Load the configuration form.
     *
     * @return void
     */
    public function getContent()
    {
        return $this->display(__FILE__, '/views/templates/admin/configuration.tpl');
    }

    /**
     * Load back dependencies.
     *
     * @return void
     */
    public function loadAsset()
    {
    }
}
