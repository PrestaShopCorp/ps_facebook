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

namespace PrestaShop\Module\Psfacebook\Database;

class Install
{
    /**
     * @var \Ps_facebook
     */
    private $module;

    public function __construct(\Ps_facebook $module)
    {
        $this->module = $module;
    }

    /**
     * installTables
     *
     * @return bool
     */
    public function installTables()
    {
        $sql = [];

        $sql[] = ' CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'psfacebook` (
            `id_psfacebook` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `token` varchar(250) NOT NULL,
            PRIMARY KEY (`id_psfacebook`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';

        foreach ($sql as $query) {
            if (!\Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * installTab
     *
     * @return bool
     */
    public function installTab()
    {
        $id_tab = (int) \Tab::getIdFromClassName($this->module->controllerAdmin);
        if ($id_tab) {
            return true;
        }

        $tab = new \Tab();
        $tab->active = true;
        $tab->class_name = $this->module->controllerAdmin;
        $tab->name = [];
        foreach (\Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->module->name;
        }
        $tab->id_parent = -1;
        $tab->module = $this->module->name;

        return (bool) $tab->add();
    }
}
