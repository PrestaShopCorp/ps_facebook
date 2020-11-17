<?php

namespace PrestaShop\Module\PrestashopFacebook\Exception;

use Exception;

class FacebookClientException extends Exception
{
    const FACEBOOK_CLIENT_GET_FUNCTION_EXCEPTION = 1;

    const FACEBOOK_CLIENT_POST_FUNCTION_EXCEPTION = 2;
}
