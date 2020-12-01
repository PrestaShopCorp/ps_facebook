<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Integration\Handler;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;
use PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock\ConfigurationAdapterMock;
use PrestaShop\Module\PrestashopFacebook\Tests\Unit\Mock\ErrorHandlerMock;

class ApiConversionHandlerTest extends TestCase
{
    /**
     * @var array
     */
    private $fbConfig;

    /**
     * @var ApiConversionHandler
     */
    private $apiConversionHandler;

    protected function setUp()
    {
        $this->fbConfig = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);

        if (empty($this->fbConfig['system_access_token'])) {
            $this->markTestSkipped(
              'The Access Token is missing from the JSON config file.'
            );
        }

        if (empty($this->fbConfig['pixel_id'])) {
            $this->markTestSkipped(
              'The Pixel ID is missing from the JSON config file.'
            );
        }

        $configurationAdapter = new ConfigurationAdapterMock(1);
        $configurationAdapter->updateValue(Config::PS_PIXEL_ID, $this->fbConfig['pixel_id']);
        $configurationAdapter->updateValue(Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN, $this->fbConfig['system_access_token']);
        if (!empty($this->fbConfig['test_event_code'])) {
            $configurationAdapter->updateValue(Config::PS_FACEBOOK_CAPI_TEST_EVENT_CODE, $this->fbConfig['test_event_code']);
        }

        $this->apiConversionHandler = new ApiConversionHandler(
            $configurationAdapter,
            new ErrorHandlerMock()
        );

        $_SERVER['REMOTE_ADDR'] = '';
        $_SERVER['HTTP_USER_AGENT'] = '';
    }

    public function testSendEvents()
    {
        $eventData = [
            'event_type' => 'ViewContent',
            'event_time' => 1606818306,
            'user' => [
                'city' => null,
                'countryIso' => null,
                'postCode' => null,
                'phone' => null,
                'stateIso' => null,
                'gender' => null,
                'birthday' => null,
                'firstname' => null,
                'lastname' => null,
                'email' => null,
            ],
            'custom_data' => [
                'currency' => 'eur',
                'contents' => [],
                'content_type' => 'product',
                'value' => 622.9,
            ],
            'event_source_url' => 'https://some.url.com',
        ];
        $this->apiConversionHandler->handleEvent($eventData);
    }
}
