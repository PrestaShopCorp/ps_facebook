<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

class AccessTokenProvider
{
    /**
     * @return string
     */
    public function getAccessToken()
    {
        // TODO : implement some cache system to get access token
        // https://graph.facebook.com/oauth/access_token?client_secret=b3f469de46ebc1f94f5b8e3e0db09fc4&grant_type=client_credentials&client_id=726899634800479
        return '';
    }
}