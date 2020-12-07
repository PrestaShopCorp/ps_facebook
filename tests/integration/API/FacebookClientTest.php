<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Integration\API;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\API\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
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

    /**
     * @var FacebookClient
     */
    private $facebookClient;

    protected function setUp()
    {
        $this->fbConfig = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);

        if (empty($this->fbConfig['access_token'])) {
            $this->markTestSkipped(
              'The Access Token is missing from the JSON config file.'
            );
        }

        $configurationAdapter = new ConfigurationAdapterMock(1);
        $configurationAdapter->updateValue(Config::PS_FACEBOOK_PIXEL_ENABLED, true);

        $this->facebookClient = new FacebookClient(
            new FacebookEssentialsApiClientFactory(),
            new AccessTokenProviderMock($this->fbConfig['access_token']),
            $configurationAdapter,
            new ErrorHandlerMock()
        );
    }

    /**
     * Basic test for having a default constructor with all working parameters
     */
    public function testFacebookClientIsReady()
    {
        $this->assertTrue($this->facebookClient->hasAccessToken());
    }

    public function testGetFbUserEmail()
    {
        $this->assertNotNull($this->facebookClient->getUserEmail()->getEmail());
    }

    public function testGetBusinessManager()
    {
        if (empty($this->fbConfig['business_manager_id'])) {
            $this->markTestSkipped(
              'The Business Manager ID is missing from the JSON config file.'
            );
        }

        $businessManagerId = $this->fbConfig['business_manager_id'];

        $businessManager = $this->facebookClient->getBusinessManager($businessManagerId);

        $this->assertNotNull($businessManager->getId());
        $this->assertSame($businessManagerId, $businessManager->getId());
        $this->assertNotNull($businessManager->getName());
        $this->assertNotNull($businessManager->getCreatedAt());
    }

    public function testGetPixel()
    {
        if (empty($this->fbConfig['ad_id'])) {
            $this->markTestSkipped(
              'The Ad ID is missing from the JSON config file.'
            );
        }

        if (empty($this->fbConfig['pixel_id'])) {
            $this->markTestSkipped(
              'The Pixel ID is missing from the JSON config file.'
            );
        }

        $adId = $this->fbConfig['ad_id'];
        $pixelId = $this->fbConfig['pixel_id'];

        $pixel = $this->facebookClient->getPixel($adId, $pixelId);

        $this->assertNotNull($pixel->getId());
        $this->assertSame($pixelId, $pixel->getId());
        $this->assertNotNull($pixel->getName());
        $this->assertNotNull($pixel->getLastActive());
        $this->assertNotNull($pixel->isUnavailable());
        $this->assertTrue($pixel->isActive());
    }

    public function testGetPage()
    {
        if (empty($this->fbConfig['page_ids'])) {
            $this->markTestSkipped(
              'The Page IDs are missing from the JSON config file.'
            );
        }

        $pageIds = $this->fbConfig['page_ids'];

        $page = $this->facebookClient->getPage($pageIds);

        $this->assertNotNull($page->getId());
        $this->assertSame(reset($pageIds), $page->getId());
        $this->assertNotNull($page->getPage());
        $this->assertNotNull($page->getLikes());
        $this->assertNotNull($page->getLogo());
    }

    public function testGetAd()
    {
        if (empty($this->fbConfig['ad_id'])) {
            $this->markTestSkipped(
              'The Ad ID is missing from the JSON config file.'
            );
        }

        $adId = $this->fbConfig['ad_id'];

        $ad = $this->facebookClient->getAd($adId);

        $this->assertNotNull($ad->getId());
        $this->assertSame('act_' . $adId, $ad->getId());
        $this->assertNotNull($ad->getName());
        $this->assertNotNull($ad->getCreatedAt());
    }

    public function testGetFbeAttribute()
    {
        if (empty($this->fbConfig['external_business_id'])) {
            $this->markTestSkipped(
              'The External Business ID is missing from the JSON config file.'
            );
        }

        $externalBusinessId = $this->fbConfig['external_business_id'];

        $fbeData = $this->facebookClient->getFbeAttribute($externalBusinessId);

        $this->assertTrue(is_array($fbeData));

        // Check mandatory keys exist
        $this->assertArrayHasKey('business_manager_id', $fbeData);
        $this->assertArrayHasKey('pixel_id', $fbeData);
        $this->assertArrayHasKey('profiles', $fbeData);
        $this->assertArrayHasKey('ad_account_id', $fbeData);
        $this->assertArrayHasKey('catalog_id', $fbeData);
        $this->assertArrayHasKey('pages', $fbeData);

        // Check type
        $this->assertTrue(is_string($fbeData['business_manager_id']));
        $this->assertTrue(is_string($fbeData['pixel_id']));
        $this->assertTrue(is_array($fbeData['profiles']));
        $this->assertTrue(is_string($fbeData['ad_account_id']));
        $this->assertTrue(is_string($fbeData['catalog_id']));
        $this->assertTrue(is_array($fbeData['pages']));

        // If properties are defined in config.json, check they match with the returned data
        if (!empty($this->fbConfig['business_manager_id'])) {
            $this->assertSame($this->fbConfig['business_manager_id'], $fbeData['business_manager_id']);
        }
        if (!empty($this->fbConfig['pixel_id'])) {
            $this->assertSame($this->fbConfig['pixel_id'], $fbeData['pixel_id']);
        }
        // TODO: check profiles?
        if (!empty($this->fbConfig['ad_id'])) {
            $this->assertSame($this->fbConfig['ad_id'], $fbeData['ad_account_id']);
        }
        // TODO: check catalog ID?
        if (!empty($this->fbConfig['page_ids'])) {
            $this->assertSame($this->fbConfig['page_ids'], $fbeData['pages']);
        }
    }

    public function testGetFbeFeatures()
    {
        if (empty($this->fbConfig['external_business_id'])) {
            $this->markTestSkipped(
              'The External Business ID is missing from the JSON config file.'
            );
        }

        $externalBusinessId = $this->fbConfig['external_business_id'];

        $fbeFeatures = $this->facebookClient->getFbeFeatures($externalBusinessId);

        $this->assertTrue(is_array($fbeFeatures));
        foreach (Config::AVAILABLE_FBE_FEATURES as $feature) {
            $this->assertArrayHasKey($feature, $fbeFeatures, "Feature $feature was not found in list from FB.");
            $this->assertTrue(isset($fbeFeatures[$feature]['enabled']), "Feature $feature has no key 'enabled'");
            $this->assertTrue(is_bool($fbeFeatures[$feature]['enabled']), "Key 'enabled' of feature $feature is not a boolean");
        }
    }

    public function testUpdateFeature()
    {
        if (empty($this->fbConfig['external_business_id'])) {
            $this->markTestSkipped(
              'The External Business ID is missing from the JSON config file.'
            );
        }

        $externalBusinessId = $this->fbConfig['external_business_id'];

        $configuration = [
            'messenger_chat' => [
                'enabled' => true,
            ],
        ];

        $result = $this->facebookClient->updateFeature($externalBusinessId, json_encode($configuration));

        $this->assertTrue(is_array($result));
        $this->assertTrue($result['success']);
    }
}
