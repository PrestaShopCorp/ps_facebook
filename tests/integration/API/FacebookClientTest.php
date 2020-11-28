<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Integration\API;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Factory\FacebookEssentialsApiClientFactory;
use PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock\AccessTokenProviderMock;
use PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock\ConfigurationAdapterMock;
use PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock\ErrorHandlerMock;

class FacebookClientTest extends TestCase
{
    /**
     * @var array
     */
    private $fbConfig;

    protected function setUp()
    {
        $this->fbConfig = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);

        if (empty($this->fbConfig['access_token'])) {
            $this->markTestSkipped(
              'The Access Token is missing from the JSON config file.'
            );
        }
    }

    public function testFacebookClientIsReady()
    {
        $facebookClient = new FacebookClient(
            new FacebookEssentialsApiClientFactory(),
            new AccessTokenProviderMock($this->fbConfig['access_token']),
            new ConfigurationAdapterMock(1),
            new ErrorHandlerMock()
        );

        $this->assertTrue($facebookClient->hasAccessToken());
    }
}
