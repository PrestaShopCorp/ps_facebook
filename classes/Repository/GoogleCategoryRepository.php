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

    /**
     * @param int $categoryId
     *
     * @return int
     */
    public function getGoogleCategoryIdByCategoryId($categoryId)
    {
        $sql = new DbQuery();
        $sql->select('google_category_id');
        $sql->from('fb_category_match');
        $sql->where('`id_Category` = "' . (int) $categoryId . '"');

        return (int) Db::getInstance()->getValue($sql);
    }
}
