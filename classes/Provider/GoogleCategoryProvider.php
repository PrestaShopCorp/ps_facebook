<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Category;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;

class GoogleCategoryProvider implements GoogleCategoryProviderInterface
{
    /**
     * @var GoogleCategoryRepository
     */
    private $googleCategoryRepository;

    public function __construct(
        GoogleCategoryRepository $googleCategoryRepository
    ) {
        $this->googleCategoryRepository = $googleCategoryRepository;
    }

    /**
     * @param int $categoryId
     * @param int $shopId
     *
     * @return array|null
     */
    public function getGoogleCategory($categoryId, $shopId)
    {
        $categoryMatch = $this->googleCategoryRepository->getCategoryMatchByCategoryId($categoryId, $shopId);
        if (!is_array($categoryMatch)) {
            return null;
        }

        return $categoryMatch;
    }

    /**
     * @param int $categoryId
     * @param int $langId
     * @param int $shopId
     * @param int $page
     *
     * @return array|null
     */
    public function getGoogleCategoryChildren($categoryId, $langId, $shopId, $page = 1)
    {
        if ($page < 1) {
            $page = 1;
        }
        $googleCategories = $this->googleCategoryRepository->getFilteredCategories(
            $categoryId,
            $langId,
            Config::CATEGORIES_PER_PAGE * ($page - 1),
            Config::CATEGORIES_PER_PAGE,
            $shopId
        );

        if (!is_array($googleCategories)) {
            return null;
        }

        return $googleCategories;
    }

    public function getCategoryPaths($topCategoryId, $langId, $shopId)
    {
        if ((int) $topCategoryId === 0) {
            return [
                'category_path' => '',
                'category_id_path' => '',
            ];
        }
        $categoryId = $topCategoryId;
        $categories = [];
        try {
            $categoriesWithParentsInfo = $this->googleCategoryRepository->getCategoriesWithParentInfo($langId, $shopId);
        } catch (\PrestaShopDatabaseException $e) {
            return [
                'category_path' => '',
                'category_id_path' => '',
            ];
        }
        $homeCategory = Category::getTopCategory()->id;

        while ((int) $categoryId != $homeCategory) {
            foreach ($categoriesWithParentsInfo as $category) {
                if ($category['id_category'] == $categoryId) {
                    $categories[] = $category;
                    $categoryId = $category['id_parent'];
                    break;
                }
            }
        }
        $categories = array_reverse($categories);

        return [
            'category_path' => implode(' > ', array_map(function ($category) {
                return $category['name'];
            }, $categories)),
            'category_id_path' => implode(' > ', array_map(function ($category) {
                return $category['id_category'];
            }, $categories)),
        ];
    }
}
