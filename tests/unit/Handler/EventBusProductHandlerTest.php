<?php

namespace Handler;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\Handler\EventBusProductHandler;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

class EventBusProductHandlerTest extends TestCase
{
    /**
     * @dataProvider getInformationAboutEventBusProductsDataProvider
     *
     * @param $eventBusProduct
     * @param $productRepoMocks
     * @param $result
     */
    public function testGetInformationAboutEventBusProducts($eventBusProduct, $productRepoMocks, $result)
    {
        $productRepo = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        foreach ($productRepoMocks as $key => $mock) {
            $productRepo
                ->expects($this->at($key))
                ->method('getInformationAboutEventBusProduct')
                ->willReturn($mock);
        }
        $psFacebookTranslations = $this->getMockBuilder(PsFacebookTranslations::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventBusProductHandler = new EventBusProductHandler($productRepo, $psFacebookTranslations);
        $informationAboutProducts = $eventBusProductHandler->getInformationAboutEventBusProductsWithErrors($eventBusProduct, 1, 'eu');

        self::assertEquals($result, $informationAboutProducts);
    }

    public function getInformationAboutEventBusProductsDataProvider()
    {
        $isoCode = 'en';

        $productOneGoogleId = '1-1';
        $productOneId = 1;
        $productOneAttributeId = 1;
        $productOneName = 'Hummingbird printed t-shirt';

        $productTwoGoogleId = '2-2';
        $productTwoId = 2;
        $productTwoAttributeId = 2;
        $productTwoName = 'Hummingbird printed t-shirt2';

        return [
            '1 product' => [
                'eventBusProduct' => [$productOneGoogleId => ['base' => 'error message', 'l10n' => 'error message']],
                'productRepoMocks' => [
                    0 => [
                        [
                            'id_product' => $productOneId,
                            'id_product_attribute' => $productOneAttributeId,
                            'name' => $productOneName,
                            'iso_code' => $isoCode,
                        ],
                    ],
                ],
                'result' => [
                    $productOneGoogleId => [
                        'messages' => ['base' => 'error message', 'l10n' => 'error message'],
                        'id_product' => $productOneId,
                        'id_product_attribute' => $productOneAttributeId,
                        'name' => $productOneName,
                        'iso_code' => $isoCode,
                    ],
                ],
            ],
            '2 products' => [
                'eventBusProduct' => [
                    $productOneGoogleId => ['base' => 'error message', 'l10n' => 'error message'],
                    $productTwoGoogleId => ['base' => 'error message', 'l10n' => 'error message'],
                ],
                'productRepoMocks' => [
                    0 => [
                        [
                            'id_product' => $productOneId,
                            'id_product_attribute' => $productOneAttributeId,
                            'name' => $productOneName,
                            'iso_code' => $isoCode,
                        ],
                    ],
                    1 => [
                        [
                            'id_product' => $productTwoId,
                            'id_product_attribute' => $productTwoAttributeId,
                            'name' => $productTwoName,
                            'iso_code' => $isoCode,
                        ],
                    ],
                ],
                'result' => [
                    $productOneGoogleId => [
                        'messages' => ['base' => 'error message', 'l10n' => 'error message'],
                        'id_product' => $productOneId,
                        'id_product_attribute' => $productOneAttributeId,
                        'name' => $productOneName,
                        'iso_code' => $isoCode,
                    ],
                    $productTwoGoogleId => [
                        'messages' => ['base' => 'error message', 'l10n' => 'error message'],
                        'id_product' => $productTwoId,
                        'id_product_attribute' => $productTwoAttributeId,
                        'name' => $productTwoName,
                        'iso_code' => $isoCode,
                    ],
                ],
            ],
            '2 products but one is missing in database' => [
                'eventBusProduct' => [
                    $productOneGoogleId => ['base' => 'error message', 'l10n' => 'error message'],
                    $productTwoGoogleId => ['base' => 'error message', 'l10n' => 'error message'],
                ],
                'productRepoMocks' => [
                    0 => [
                        [
                            'messages' => ['base' => 'error message', 'l10n' => 'error message'],
                            'id_product' => $productOneId,
                            'id_product_attribute' => $productOneAttributeId,
                            'name' => $productOneName,
                            'iso_code' => $isoCode,
                        ],
                    ],
                    1 => [],
                ],
                'result' => [
                    $productOneGoogleId => [
                        'messages' => ['base' => 'error message', 'l10n' => 'error message'],
                        'id_product' => $productOneId,
                        'id_product_attribute' => $productOneAttributeId,
                        'name' => $productOneName,
                        'iso_code' => $isoCode,
                    ],
                    $productTwoGoogleId => [
                        'messages' => ['base' => 'error message', 'l10n' => 'error message'],
                    ],
                ],
            ],
        ];
    }
}
