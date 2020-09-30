<?php

namespace PrestaShop\Module\PrestashopFacebook\Database;

use Language;
use Tab;

class Installer
{
    private $module;

    public function __construct(\Ps_facebook $module)
    {
        $this->module = $module;
    }

    public function install()
    {
        return $this->installConfiguration() &&
            $this->module->registerHook(\Ps_facebook::HOOK_LIST) &&
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
            foreach (\Ps_facebook::CONFIGURATION_LIST as $name => $value) {
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
            $installTabCompleted &= $this->installTab(
                $tab['className'],
                $tab['parent'],
                $tab['name'],
                $tab['module'],
                $tab['active']
            );
        }

        return $installTabCompleted;
    }

    public function installTab($className, $parent, $name, $module, $active = true)
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

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $moduleTab->name[$language['id_lang']] = $name;
        }

        if (!$moduleTab->save()) {
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
            ],
            [
                'className' => 'AdminPsfacebookModule',
                'parent' => 'Marketing',
                'name' => 'Facebook',
                'module' => $this->module->name,
                'active' => true,
            ],
            [
                'className' => 'AdminAjaxPsfacebook',
                'parent' => -1,
                'name' => $this->module->name,
                'module' => $this->module->name,
                'active' => true,
            ],
        ];
    }
}
