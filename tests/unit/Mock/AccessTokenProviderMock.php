<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock;

use PrestaShop\Module\PrestashopFacebook\Provider\AccessTokenProvider;

class AccessTokenProviderMock extends AccessTokenProvider
{
    /**
     * @var string
     */
    private $userAccessToken;

    /**
     * @var string|null
     */
    private $systemAccessToken;

    public function __construct($userAccessToken, $systemAccessToken = null)
    {
        $this->userAccessToken = $userAccessToken;
        $this->systemAccessToken = $systemAccessToken;
    }

    /**
     * @return string
     */
    public function getUserAccessToken()
    {
        return $this->userAccessToken;
    }

    /**
     * @return string|null
     */
    public function getSystemAccessToken()
    {
        return $this->systemAccessToken;
    }
}
