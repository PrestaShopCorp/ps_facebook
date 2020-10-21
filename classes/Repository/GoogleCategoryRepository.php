<?php

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;

class GoogleCategoryRepository
{
    /**
     * @param int $googleCategoryId
     */
    public function getGoogleCategoryIdByGoogleCategoryId($googleCategoryId)
    {
        $sql = new DbQuery();
        $sql->select('id_fb_google_category');
        $sql->from('fb_google_category');
        $sql->where('`google_category_id` = "' . (int) $googleCategoryId . '"');

        return Db::getInstance()->getValue($sql);
    }
}
