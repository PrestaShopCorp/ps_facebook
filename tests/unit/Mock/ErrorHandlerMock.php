<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock;

use Module;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;
use Raven_Client;

/**
 * Handle Error.
 */
class ErrorHandlerMock extends ErrorHandler
{
    /**
     * @var ErrorHandler
     */
    private static $instance;

    public function __construct()
    {
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
       throw $error;
    }

    /**
     * @return ErrorHandler
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }
}
