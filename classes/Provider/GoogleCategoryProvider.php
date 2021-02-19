<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

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

    /**
     * @param int $shopId
     *
     * @return array
     */
    public function getInformationAboutCategoryMatches($shopId)
    {
        $numberOfMatchedCategories = $this->googleCategoryRepository->getNumberOfMatchedCategories($shopId);
        $totalCategories = $this->googleCategoryRepository->getNumberOfTotalCategories($shopId);

        return [
            'matched' => $numberOfMatchedCategories,
            'total' => $totalCategories,
        ];
    }

    /**
     * @param array $categoryIds
     * @param int $shopId
     *
     * @return array|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getGoogleCategories(array $categoryIds, $shopId)
    {
        $categoryMatch = $this->googleCategoryRepository->getCategoryMatchesByCategoryIds($categoryIds, $shopId);
        if (!is_array($categoryMatch)) {
            return null;
        }

        return $categoryMatch;
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
        $categoryExists = true;

        while ((int) $categoryId != $homeCategory && $categoryExists) {
            $categoryExists = false;
            foreach ($categoriesWithParentsInfo as $category) {
                if ($category['id_category'] == $categoryId) {
                    $categories[] = $category;
                    $categoryId = $category['id_parent'];
                    $categoryExists = true;
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
