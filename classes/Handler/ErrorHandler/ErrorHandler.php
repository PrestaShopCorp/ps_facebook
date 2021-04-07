<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler;

use Module;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Config\Env;

/**
 * Handle Error.
 */
class ErrorHandler
{
    /**
     * @var ModuleFilteredRavenClient
     */
    protected $client;

    public function __construct(Module $module, Env $env)
    {
        $this->client = new ModuleFilteredRavenClient(
            $env->get('PSX_FACEBOOK_SENTRY_CREDENTIALS'),
            [
                'level' => 'warning',
                'tags' => [
                    'php_version' => phpversion(),
                    'ps_facebook_version' => $module->version,
                    'prestashop_version' => _PS_VERSION_,
                    'ps_facebook_is_enabled' => Module::isEnabled($module->name),
                    'ps_facebook_is_installed' => Module::isInstalled($module->name),
                    'facebook_app_id' => Config::PSX_FACEBOOK_APP_ID,
                ],
            ]
        );
        // We use realpath to get errors even if module is behind a symbolic link
        $this->client->setAppPath(realpath(_PS_MODULE_DIR_ . $module->name . '/'));
        // Useless as it will exclude everything even if specified in the app path
        //$this->client->setExcludedAppPaths([_PS_ROOT_DIR_]);
        $this->client->install();
    }

    /**
     * @param \Exception $error
     * @param mixed $code
     * @param bool|null $throw
     * @param array|null $data
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle($error, $code = null, $throw = true, $data = null)
    {
        $this->client->captureException($error, $data);
        if ($code && true === $throw) {
            http_response_code($code);
            throw $error;
        }
    }

    /**
     * @return void
     */
    private function __clone()
    {
    }
}
