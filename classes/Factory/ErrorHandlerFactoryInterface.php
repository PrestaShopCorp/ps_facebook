<?php

namespace PrestaShop\Module\PrestashopFacebook\Factory;

use PrestaShop\AccountsAuth\Handler\ErrorHandler\ErrorHandler;

interface ErrorHandlerFactoryInterface
{
    /**
     * @return ErrorHandler
     */
    public function getErrorHandler();
}
