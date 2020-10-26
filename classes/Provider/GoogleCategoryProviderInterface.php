<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use FBGoogleCategory;

interface GoogleCategoryProviderInterface
{
    /**
     * @param int $categoryId
     *
     * @return FBGoogleCategory|null
     */
    public function getGoogleCategory($categoryId);
}
