<?php

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;
use PrestaShopCollection;

class GoogleCategoryRepository
{
    /**
     * @param int $googleCategoryId
     *
     * @return int
     */
    public function getGoogleCategoryIdByGoogleCategoryId($googleCategoryId)
    {
        $sql = new DbQuery();
        $sql->select('id_fb_google_category');
        $sql->from('fb_google_category');
        $sql->where('`google_category_id` = "' . (int) $googleCategoryId . '"');

        return (int) Db::getInstance()->getValue($sql);
    }

    public function deleteNotExistingGoogleCategories(array $googleCategoryIds)
    {
        Db::getInstance()->delete(
            'fb_google_category',
            'google_category_id NOT IN (' . implode(', ', $googleCategoryIds) . ')'
        );

        Db::getInstance()->delete(
            'fb_category_match',
            'google_category_id NOT IN (' . implode(', ', $googleCategoryIds) . ')'
        );
    }

    /**
     * @param int $categoryId
     * @param int $googleCategoryId
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updateCategoryMatch($categoryId, $googleCategoryId)
    {
        Db::getInstance()->insert(
            'fb_category_match',
            [
                'id_category' => (int) $categoryId,
                'google_category_id' => (int) $googleCategoryId,
            ],
            false,
            true,
            DB::REPLACE
        );
    }

    /**
     * @param PrestaShopCollection $childCategories
     * @param int $googleCategoryId
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updateCategoryChildrenMatch(PrestaShopCollection $childCategories, $googleCategoryId)
    {
        $data = [];

        foreach ($childCategories as $category) {
            $data[] = [
                'id_category' => (int) $category->id,
                'google_category_id' => (int) $googleCategoryId,
            ];
        }

        Db::getInstance()->insert(
            'fb_category_match',
            $data,
            false,
            true,
            DB::REPLACE
        );
    }
}
