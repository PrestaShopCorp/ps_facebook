<?php

namespace PrestaShop\Module\PrestashopFacebook\Database;

use Exception;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookInstallerException;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\PrestashopFacebook\Repository\TabRepository;
use PrestaShop\Module\Ps_facebook\Tracker\Segment;

class Uninstaller
{
    const CLASS_NAME = 'Uninstaller';

    /**
     * @var array
     */
    private $errors = [];

    private $module;

    /**
     * @var TabRepository
     */
    private $tabRepository;

    /**
     * @var Segment
     */
    private $segment;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    public function __construct(
        \Ps_facebook $module,
        TabRepository $tabRepository,
        Segment $segment,
        ErrorHandler $errorHandler
    ) {
        $this->module = $module;
        $this->tabRepository = $tabRepository;
        $this->segment = $segment;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function uninstall()
    {
        $this->segment->setMessage('PS Facebook uninstalled');
        $this->segment->track();

        foreach (array_keys(\Ps_facebook::CONFIGURATION_LIST) as $name) {
            \Configuration::deleteByName((string) $name);
        }

        return $this->uninstallTabs() && $this->uninstallTables();
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException|Exception
     */
    private function uninstallTabs()
    {
        $uninstallTabCompleted = true;

        try {
            foreach (\Ps_facebook::MODULE_ADMIN_CONTROLLERS as $controllerName) {
                $id_tab = (int) \Tab::getIdFromClassName($controllerName);
                $tab = new \Tab($id_tab);
                if (\Validate::isLoadedObject($tab)) {
                    $uninstallTabCompleted = $uninstallTabCompleted && $tab->delete();
                }
            }
            $uninstallTabCompleted = $uninstallTabCompleted && $this->uninstallMarketingTab();
        } catch (Exception $e) {
            $this->errorHandler->handle(
                new FacebookInstallerException(
                    'Failed to uninstall module tabs',
                    FacebookInstallerException::FACEBOOK_UNINSTALL_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );
            $this->errors[] = $this->module->l('Failed to uninstall database tables', self::CLASS_NAME);

            return false;
        }

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

    public function uninstallTables()
    {
        try {
            include dirname(__FILE__) . '/../../sql/uninstall.php';
        } catch (\Exception $e) {
            $this->errorHandler->handle(
                new FacebookInstallerException(
                    'Failed to uninstall database tables',
                    FacebookInstallerException::FACEBOOK_UNINSTALL_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );
            $this->errors[] = $this->module->l('Failed to uninstall database tables', self::CLASS_NAME);

            return false;
        }

        return true;
    }
}
