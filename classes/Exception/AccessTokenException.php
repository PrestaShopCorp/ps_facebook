<?php

namespace PrestaShop\Module\PrestashopFacebook\Exception;

use Exception;

class AccessTokenException extends Exception
{
    const ACCESS_TOKEN_REFRESH_EXCEPTION = 1;
}
