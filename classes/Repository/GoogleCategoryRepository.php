<?php

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;

class GoogleCategoryRepository
{
    /**
     * @param int $googleCategoryId
     *
     * @return false|string|null
     */
    public function getGoogleCategoryIdByGoogleCategoryId($googleCategoryId)
    {
        $sql = new DbQuery();
        $sql->select('id_fb_google_category');
        $sql->from('fb_google_category');
        $sql->where('`google_category_id` = "' . (int) $googleCategoryId . '"');

        return Db::getInstance()->getValue($sql);
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
}
