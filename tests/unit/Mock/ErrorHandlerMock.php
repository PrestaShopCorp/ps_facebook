<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock;

use GuzzleHttp\Exception\RequestException;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;

/**
 * Handle Error.
 */
class ErrorHandlerMock extends ErrorHandler
{
    const DEBUG = 1;

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
        if (static::DEBUG && $error->getPrevious() instanceof RequestException) {
            /** @var RequestException */
            $requestException = $error->getPrevious();
            if ($requestException->hasResponse()) {
                // Display the response from FB and keep the trace
                throw new \Exception($requestException->getResponse()->getBody()->getContents(), $code, $error);
            }
        }

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
