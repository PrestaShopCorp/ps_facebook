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

    /** 
     * Basic test for having a default constructor with all working parameters
     */
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

    public function testGetFbUserEmail()
    {
        $facebookClient = new FacebookClient(
            new FacebookEssentialsApiClientFactory(),
            new AccessTokenProviderMock($this->fbConfig['access_token']),
            new ConfigurationAdapterMock(1),
            new ErrorHandlerMock()
        );

        $this->assertNotNull($facebookClient->getUserEmail()->getEmail());
    }

    public function testGetBusinessManager()
    {
        if (empty($this->fbConfig['business_manager_id'])) {
            $this->markTestSkipped(
              'The Business Manager ID is missing from the JSON config file.'
            );
        }

        $facebookClient = new FacebookClient(
            new FacebookEssentialsApiClientFactory(),
            new AccessTokenProviderMock($this->fbConfig['access_token']),
            new ConfigurationAdapterMock(1),
            new ErrorHandlerMock()
        );

        $businessManagerId = $this->fbConfig['business_manager_id'];

        $businessManager = $facebookClient->getBusinessManager($businessManagerId);

        $this->assertNotNull($businessManager->getId());
        $this->assertSame($businessManagerId, $businessManager->getId());
        $this->assertNotNull($businessManager->getName());
        $this->assertNotNull($businessManager->getCreatedAt());
    }
}
