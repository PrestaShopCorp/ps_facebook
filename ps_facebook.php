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
    public $hook = [
        'displayHeader',
    ];

    /**
     * Symfony DI Container
     */
    private $moduleContainer;

    public function __construct()
    {
        $this->name = 'ps_facebook';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        $this->module_key = '';

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

        $this->compile();
    }

    /**
     * This method is trigger at the installation of the module
     * - install all module tables
     * - set some configuration value
     * - register hook used by the module.
     *
     * @return void
     */
    public function install()
    {
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
    }

    /**
     * Load back dependencies.
     *
     * @return void
     */
    public function loadAsset()
    {
    }

    /**
     * @param bool $id
     *
     * @return mixed
     */
    public function getContainer($id = false)
    {
        if ($id) {
            return $this->moduleContainer->get($id);
        }

        return $this->moduleContainer;
    }

    private function compile()
    {
        $containerBuilder = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $locator = new \Symfony\Component\Config\FileLocator($this->getLocalPath() . 'config');
        $loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($containerBuilder, $locator);
        $loader->load('config.yml');
        $containerBuilder->compile();

        $this->moduleContainer = $containerBuilder;
    }
}
