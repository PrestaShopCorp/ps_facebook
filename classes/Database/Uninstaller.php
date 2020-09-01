<?php

namespace PrestaShop\Module\PrestashopFacebook\Database;

class Uninstaller
{
    private $module;

    public function __construct(\Ps_facebook $module)
    {
        $this->module = $module;
    }

    public function uninstall()
    {
        foreach (array_keys(\Ps_facebook::CONFIGURATION_LIST) as $name) {
            \Configuration::deleteByName((string) $name);
        }

        return $this->uninstallTabs();
    }

    private function uninstallTabs()
    {
        $uninstallTabCompleted = true;

        foreach (\Ps_facebook::MODULE_ADMIN_CONTROLLERS as $controllerName) {
            $id_tab = (int) \Tab::getIdFromClassName($controllerName);
            $tab = new \Tab($id_tab);
            if (\Validate::isLoadedObject($tab)) {
                $uninstallTabCompleted = $uninstallTabCompleted && $tab->delete();
            }
        }

        return $uninstallTabCompleted;
    }
}
