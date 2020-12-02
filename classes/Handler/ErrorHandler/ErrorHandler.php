<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler;

use Module;
use Raven_Client;

/**
 * Handle Error.
 */
class ErrorHandler
{
    /**
     * @var Raven_Client
     */
    protected $client;

    /**
     * @var ErrorHandler
     */
    private static $instance;

    public function __construct()
    {
        $module = Module::getInstanceByName('ps_facebook');

        $this->client = new Raven_Client(
            $_ENV['SENTRY_CREDENTIALS'],
            [
                'level' => 'warning',
                'tags' => [
                    'php_version' => phpversion(),
                    'ps_facebook_version' => $module->version,
                    'prestashop_version' => _PS_VERSION_,
                    'ps_facebook_is_enabled' => \Module::isEnabled('ps_facebook'),
                    'ps_facebook_is_installed' => \Module::isInstalled('ps_facebook'),
                ],
            ]
        );

        $this->client->install();
    }

    /**
     * @param \Exception $error
     * @param mixed $code
     * @param bool|null $throw
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle($error, $code = null, $throw = true)
    {
        $code ? $this->client->captureException($error) : $this->client->captureMessage($error);
        if ($code && true === $throw) {
            http_response_code($code);
            throw $error;
        }
    }

    /**
     * @return ErrorHandler
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new ErrorHandler();
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    private function __clone()
    {
    }
}
