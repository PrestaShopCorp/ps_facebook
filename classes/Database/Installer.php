<?php

namespace PrestaShop\Module\PrestashopFacebook\Database;

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

        foreach (\Ps_facebook::MODULE_ADMIN_CONTROLLERS as $controllerName) {
            if (\Tab::getIdFromClassName($controllerName)) {
                continue;
            }

            $tab = new \Tab();
            $tab->class_name = $controllerName;
            $tab->active = true;
            $tab->name = array_fill_keys(
                \Language::getIDs(false),
                $this->module->displayName
            );
            $tab->id_parent = -1;
            $tab->module = $this->module->name;
            $installTabCompleted = $installTabCompleted && $tab->add();
        }

        return $installTabCompleted;
    }
}
