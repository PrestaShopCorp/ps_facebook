<?php

namespace PrestaShop\Module\PrestashopFacebook\Exception;

use Exception;

class FacebookInstallerException extends Exception
{
    const FACEBOOK_INSTALL_EXCEPTION = 1;

    const FACEBOOK_UNINSTALL_EXCEPTION = 2;
}
