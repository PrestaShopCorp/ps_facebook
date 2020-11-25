<?php

namespace provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\Provider\GoogleCategoryProvider;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;

class GoogleCategoryProviderTest extends TestCase
{
    /**
     * @dataProvider getGoogleCategoryDataProvider
     *
     * @param $categoryId
     * @param $categoryMatchMock
     * @param $result
     *
     * @throws \PrestaShopDatabaseException
     */
    public function testGetGoogleCategory($categoryId, $categoryMatchMock, $result)
    {
        $googleCategoryRepo = $this->getMockBuilder(GoogleCategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $googleCategoryRepo->method('getCategoryMatchByCategoryId')->willReturn($categoryMatchMock);

        $googleCategoryProvider = new GoogleCategoryProvider($googleCategoryRepo);
        $googleCategory = $googleCategoryProvider->getGoogleCategory($categoryId, 1);

        $this->assertEquals($result, $googleCategory);
    }

    /**
     * @dataProvider getGoogleCategoryChildrenDataProvider
     *
     * @param $categoryId
     * @param $categoryMatchMock
     * @param $langId
     * @param $page
     * @param $result
     */
    public function testGetGoogleCategoryChildren($categoryId, $categoryMatchMock, $langId, $page, $result)
    {
        $googleCategoryRepo = $this->getMockBuilder(GoogleCategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $googleCategoryRepo->method('getFilteredCategories')->willReturn($categoryMatchMock);
        $googleCategoryProvider = new GoogleCategoryProvider($googleCategoryRepo);
        $googleCategory = $googleCategoryProvider->getGoogleCategoryChildren($categoryId, $langId, 1, $page);

        $this->assertEquals($result, $googleCategory);
    }

    public function getGoogleCategoryDataProvider()
    {
        return [
            'Not saved category' => [
                'categoryId' => 1,
                'categoryMatchMock' => false,
                'result' => null,
            ],
            'Saved category without parent check' => [
                'categoryId' => 1,
                'categoryMatchMock' => [
                    'id_category' => '1',
                    'google_category_id' => '166',
                    'is_parent_category' => '0',
                ],
                'result' => [
                    'id_category' => '1',
                    'google_category_id' => '166',
                    'is_parent_category' => '0',
                ],
            ],
            'Saved category with parent check' => [
                'categoryId' => 1,
                'categoryMatchMock' => [
                    'id_category' => '1',
                    'google_category_id' => '166',
                    'is_parent_category' => '1',
                ],
                'result' => [
                    'id_category' => '1',
                    'google_category_id' => '166',
                    'is_parent_category' => '1',
                ],
            ],
        ];
    }

    public function getGoogleCategoryChildrenDataProvider()
    {
        return [
            'main categories' => [
                'categoryId' => 2,
                'categoryMatchMock' => [
                    0 => [
                            'id_category' => '3',
                            'name' => 'Clothes',
                            'google_category_id' => '8',
                            'is_parent_category' => '0',
                        ],
                    1 => [
                            'id_category' => '6',
                            'name' => 'Accessories',
                            'google_category_id' => '10',
                            'is_parent_category' => '0',
                        ],
                    2 => [
                            'id_category' => '9',
                            'name' => 'Art',
                            'google_category_id' => null,
                            'is_parent_category' => null,
                        ],
                ],
                'langId' => 1,
                'page' => 1,
                'result' => [
                    0 => [
                            'id_category' => '3',
                            'name' => 'Clothes',
                            'google_category_id' => '8',
                            'is_parent_category' => '0',
                        ],
                    1 => [
                            'id_category' => '6',
                            'name' => 'Accessories',
                            'google_category_id' => '10',
                            'is_parent_category' => '0',
                        ],
                    2 => [
                            'id_category' => '9',
                            'name' => 'Art',
                            'google_category_id' => null,
                            'is_parent_category' => null,
                        ],
                ],
            ],
        ];
    }
}
