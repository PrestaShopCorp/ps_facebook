<?php

namespace PrestaShop\Module\PrestashopFacebook\Database;

use PrestaShop\Module\PrestashopFacebook\Repository\TabRepository;

class Uninstaller
{
    private $module;

    /**
     * @var TabRepository
     */
    private $tabRepository;

    public function __construct(\Ps_facebook $module, TabRepository $tabRepository)
    {
        $this->module = $module;
        $this->tabRepository = $tabRepository;
    }

    public function uninstall()
    {
        foreach (array_keys(\Ps_facebook::CONFIGURATION_LIST) as $name) {
            \Configuration::deleteByName((string) $name);
        }

        return $this->uninstallTabs() && $this->uninstallTables();
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
        $uninstallTabCompleted = $uninstallTabCompleted && $this->uninstallMarketingTab();

        return $uninstallTabCompleted;
    }

    public function uninstallTables()
    {
        try {
            include dirname(__FILE__) . '/../../sql/uninstall.php';
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    private function uninstallMarketingTab()
    {
        $id_tab = (int) \Tab::getIdFromClassName('Marketing');
        $tab = new \Tab($id_tab);
        if (!\Validate::isLoadedObject($tab)) {
            return true;
        }
        if ($this->tabRepository->hasChildren($id_tab)) {
            return true;
        }

        return $tab->delete();
    }
}
