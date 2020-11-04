<?php

namespace PrestaShop\Module\PrestashopFacebook\Database;

use Exception;
use PrestaShop\AccountsAuth\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookInstallerException;
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

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function uninstall()
    {
        try {
            foreach (array_keys(\Ps_facebook::CONFIGURATION_LIST) as $name) {
                \Configuration::deleteByName((string) $name);
            }

            return $this->uninstallTabs();
        } catch (Exception $e) {
            $errorHandler = ErrorHandler::getInstance();
            $errorHandler->handle(
                new FacebookInstallerException(
                    'Failed to uninstall module. ' . $e->getMessage(),
                    FacebookInstallerException::FACEBOOK_UNINSTALL_EXCEPTION,
                    $e
                ),
                FacebookInstallerException::FACEBOOK_UNINSTALL_EXCEPTION,
                false
            );
        }
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
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

    /**
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
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
