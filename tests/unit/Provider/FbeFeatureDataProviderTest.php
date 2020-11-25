<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Provider\FbeFeatureDataProvider;
use PrestaShop\Module\PrestashopFacebook\Tests\Mock\ConfigurationAdapterMock;
use PrestaShop\Module\PrestashopFacebook\Tests\Mock\FacebookClientMock;
use Shop;

class FbeFeatureDataProviderTest extends TestCase
{
    public function testFeaturesExist()
    {
        $shop = new Shop();
        $shop->id = 1;
        $actual = (new FbeFeatureDataProvider(new FacebookClientMock(), new ConfigurationAdapterMock($shop)))->getFbeFeatures();

        $this->assertAllFeaturesExistOneTime($actual);
    }

    public function testEnabledFacebookFeatureIsFoundInEnabledFeaturesReponse()
    {
        $shop = new Shop();
        $shop->id = 1;
        $configurationAdapter = new ConfigurationAdapterMock($shop);
        $facebookClient = (new FacebookClientMock())
            ->switchFeature('messenger_chat', true);
        $actual = (new FbeFeatureDataProvider($facebookClient, $configurationAdapter))->getFbeFeatures();

        $this->assertAllFeaturesExistOneTime($actual);
        $this->assertTrue($actual['enabledFeatures']['messenger_chat']['enabled']);
    }

    private function assertAllFeaturesExistOneTime($response)
    {
        foreach (Config::AVAILABLE_FBE_FEATURES as $feature) {
            // Check feature exist in and only one key
            $count = array_key_exists($feature, $response['enabledFeatures']) +
                array_key_exists($feature, $response['disabledFeatures']) +
                array_key_exists($feature, $response['unavailableFeatures']);

            $this->assertEquals(
                1,
                $count,
                sprintf(
                    'Each FB feature should be found only 1 time, but key "%s" has been found %d time(s)',
                    $feature,
                    $count
                )
            );
        }
    }
}
