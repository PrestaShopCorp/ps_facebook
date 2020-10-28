<?php

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;
use PrestaShopCollection;

class GoogleCategoryRepository
{
    /**
     * @param int $categoryId
     * @param int $googleCategoryId
     * @param bool $isParentCategory
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updateCategoryMatch($categoryId, $googleCategoryId, $isParentCategory = false)
    {
        Db::getInstance()->insert(
            'fb_category_match',
            [
                'id_category' => (int) $categoryId,
                'google_category_id' => (int) $googleCategoryId,
                'is_parent_category' => $isParentCategory,
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
                'is_parent_category' => false,
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

    /**
     * @param int $categoryId
     *
     * @return array|false
     * @throws \PrestaShopDatabaseException
     */
    public function getCategoryMatchByCategoryId($categoryId)
    {
        $sql = new DbQuery();
        $sql->select('id_category');
        $sql->select('google_category_id');
        $sql->select('is_parent_category');
        $sql->from('fb_category_match');
        $sql->where('`id_category` = "' . (int) $categoryId . '"');

        return Db::getInstance()->executeS($sql);
    }
}
