<?php

namespace PrestaShop\Module\PrestashopFacebook\Database;

use Exception;
use Language;
use PrestaShop\AccountsAuth\Handler\ErrorHandler\ErrorHandler;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookInstallerException;
use PrestaShop\Module\PrestashopFacebook\Factory\ErrorHandlerFactoryInterface;
use PrestaShop\Module\Ps_facebook\Tracker\Segment;
use Tab;

class Installer
{
    private $module;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Segment
     */
    private $segment;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    public function __construct(\Ps_facebook $module, Segment $segment, ErrorHandlerFactoryInterface $errorHandlerFactory)
    {
        $this->module = $module;
        $this->segment = $segment;
        $this->errorHandler = $errorHandlerFactory->getErrorHandler();
    }

    /**
     * @return bool
     */
    public function install()
    {
        $this->segment->setMessage('PS Facebook installed');
        $this->segment->track();

        return $this->installConfiguration() &&
            $this->module->registerHook(\Ps_facebook::HOOK_LIST) &&
            $this->installTabs() &&
            $this->installTables();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
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
            try {
                $installTabCompleted = $installTabCompleted && $this->installTab(
                        $tab['className'],
                        $tab['parent'],
                        $tab['name'],
                        $tab['module'],
                        $tab['active'],
                        $tab['icon']
                    );
            } catch (Exception $e) {
                $this->errorHandler->handle(
                    new FacebookInstallerException(
                        'Failed to install module tabs',
                        FacebookInstallerException::FACEBOOK_INSTALL_EXCEPTION,
                        $e
                    ),
                    FacebookInstallerException::FACEBOOK_INSTALL_EXCEPTION,
                    false
                );
                $this->errors[] = sprintf($this->module->l('Failed to install %1s tab'), $tab['className']);

                return false;
            }
        }

        return $installTabCompleted;
    }

    public function installTab($className, $parent, $name, $module, $active, $icon)
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
        if (property_exists($moduleTab, 'icon')) {
            $moduleTab->icon = $icon;
        }

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $moduleTab->name[$language['id_lang']] = $name;
        }

        return $moduleTab->add();
    }

    public function installTables()
    {
        try {
            include dirname(__FILE__) . '/../../sql/install.php';
        } catch (\Exception $e) {
            $this->errorHandler->handle(
                new FacebookInstallerException(
                    'Failed to install database tables',
                    FacebookInstallerException::FACEBOOK_INSTALL_EXCEPTION,
                    $e
                ),
                FacebookInstallerException::FACEBOOK_INSTALL_EXCEPTION,
                false
            );
            $this->errors[] = $this->module->l('Failed to install database tables');

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
                'icon' => 'campaign',
            ],
            [
                'className' => 'AdminPsfacebookModule',
                'parent' => 'Marketing',
                'name' => 'Facebook',
                'module' => $this->module->name,
                'active' => true,
                'icon' => '',
            ],
            [
                'className' => 'AdminAjaxPsfacebook',
                'parent' => -1,
                'name' => $this->module->name,
                'module' => $this->module->name,
                'active' => true,
                'icon' => '',
            ],
        ];
    }
}
