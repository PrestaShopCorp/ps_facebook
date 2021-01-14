<?php

namespace Handler;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\Handler\GoogleProductHandler;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Translations\PsFacebookTranslations;

class GoogleProductHandlerTest extends TestCase
{
    /**
     * @dataProvider getInformationAboutGoogleProductsDataProvider
     *
     * @param $googleProduct
     * @param $productRepoMocks
     * @param $result
     */
    public function testGetInformationAboutGoogleProducts($googleProduct, $productRepoMocks, $result)
    {
        $productRepo = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        foreach ($productRepoMocks as $key => $mock) {
            $productRepo
                ->expects($this->at($key))
                ->method('getInformationAboutGoogleProduct')
                ->willReturn($mock);
        }
        $psFacebookTranslations = $this->getMockBuilder(PsFacebookTranslations::class)
            ->disableOriginalConstructor()
            ->getMock();
        $googleProductHandler = new GoogleProductHandler($productRepo, $psFacebookTranslations);
        $informationAboutProducts = $googleProductHandler->getInformationAboutGoogleProductsWithErrors($googleProduct, 1);

        self::assertEquals($result, $informationAboutProducts);
    }

    public function getInformationAboutGoogleProductsDataProvider()
    {
        $isoCode = 'en';

        $productOneGoogleId = '1-1-en';
        $productOneId = 1;
        $productOneAttributeId = 1;
        $productOneName = 'Hummingbird printed t-shirt';

        $productTwoGoogleId = '2-2-en';
        $productTwoId = 2;
        $productTwoAttributeId = 2;
        $productTwoName = 'Hummingbird printed t-shirt2';

        return [
            '1 product' => [
                'googleProduct' => [$productOneGoogleId => 'error message'],
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
                        'message' => 'error message',
                        'id_product' => $productOneId,
                        'id_product_attribute' => $productOneAttributeId,
                        'name' => $productOneName,
                        'iso_code' => $isoCode,
                    ],
                ],
            ],
            '2 products' => [
                'googleProduct' => [$productOneGoogleId => 'error message', $productTwoGoogleId => 'error message'],
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
                        'message' => 'error message',
                        'id_product' => $productOneId,
                        'id_product_attribute' => $productOneAttributeId,
                        'name' => $productOneName,
                        'iso_code' => $isoCode,
                    ],
                    $productTwoGoogleId => [
                        'message' => 'error message',
                        'id_product' => $productTwoId,
                        'id_product_attribute' => $productTwoAttributeId,
                        'name' => $productTwoName,
                        'iso_code' => $isoCode,
                    ],
                ],
            ],
            '2 products but one is missing in database' => [
                'googleProduct' => [$productOneGoogleId => 'error message', $productTwoGoogleId => 'error message'],
                'productRepoMocks' => [
                    0 => [
                        [
                            'message' => 'error message',
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
                        'message' => 'error message',
                        'id_product' => $productOneId,
                        'id_product_attribute' => $productOneAttributeId,
                        'name' => $productOneName,
                        'iso_code' => $isoCode,
                    ],
                    $productTwoGoogleId => [
                        'message' => 'error message',
                    ],
                ],
            ],
        ];
    }
}
