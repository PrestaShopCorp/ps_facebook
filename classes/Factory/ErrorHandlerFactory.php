<?php

namespace PrestaShop\Module\PrestashopFacebook\Factory;

use PrestaShop\AccountsAuth\Handler\ErrorHandler\ErrorHandler;

class ErrorHandlerFactory implements ErrorHandlerFactoryInterface
{
    /**
     * @return ErrorHandler
     */
    public function getErrorHandler()
    {
        return ErrorHandler::getInstance();
    }
}
