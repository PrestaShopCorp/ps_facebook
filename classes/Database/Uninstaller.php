<?php

namespace PrestaShop\Module\PrestashopFacebook\Database;

use PrestaShop\Module\PrestashopFacebook\Repository\TabRepository;
use PrestaShop\Module\Ps_facebook\Tracker\Segment;

class Uninstaller
{
    private $module;

    /**
     * @var TabRepository
     */
    private $tabRepository;

    /**
     * @var Segment
     */
    private $segment;

    public function __construct(\Ps_facebook $module, TabRepository $tabRepository, Segment $segment)
    {
        $this->module = $module;
        $this->tabRepository = $tabRepository;
        $this->segment = $segment;
    }

    public function uninstall()
    {
        $this->segment->setMessage('PS Facebook uninstalled');
        $this->segment->track();

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
        $uninstallTabCompleted = $uninstallTabCompleted && $this->uninstallMarketingTab();

        return $uninstallTabCompleted;
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
