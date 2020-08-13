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
    /**
     * @var string
     */
    public $tab;
    /**
     * @var string
     */
    public $version;
    /**
     * @var string
     */
    public $author;
    /**
     * @var int
     */
    public $need_instance;
    /**
     * @var string
     */
    public $module_key;
    /**
     * @var string
     */
    public $controllerAdmin;
    /**
     * @var bool
     */
    public $psVersionIs17;
    /**
     * @var string
     */
    public $css_path;
    /**
     * @var string
     */
    public $docs_path;
    /**
     * @var string
     */
    public $js_path;
    public $configurationList = [
        'fbe_account_id'
    ];

    public function __construct()
    {
        $this->name = 'ps_facebook';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
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
        $this->ps_versions_compliancy = ['min' => '1.6.1', 'max' => _PS_VERSION_];
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
        $database = new PrestaShop\Module\Psfacebook\Database\Install($this);

        return parent::install() &&
            $database->installTab() &&
            $this->registerHook($this->hook);
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
        $database = new PrestaShop\Module\Psfacebook\Database\Uninstall($this);

        return parent::uninstall() &&
            $database->uninstallTab();
    }

    /**
     * Load the configuration form.
     *
     * @return string
     */
    public function getContent()
    {
        $this->context->smarty->assign([
            'pathApp' => $this->_path . 'views/js/main.js',
            'PsfacebookControllerLink' => $this->context->link->getAdminLink('AdminAjaxPsfacebookController')
        ]);

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
