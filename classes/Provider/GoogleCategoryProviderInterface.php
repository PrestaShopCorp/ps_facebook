<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

interface GoogleCategoryProviderInterface
{
    /**
     * @param int $categoryId
     * @param int $shopId
     *
     * @return array|null
     */
    public function getGoogleCategory($categoryId, $shopId);

    /**
     * @param int $categoryId
     * @param int $page
     * @param int $shopId
     *
     * @return array|null
     */
    public function getGoogleCategoryChildren($categoryId, $page, $shopId);
}
