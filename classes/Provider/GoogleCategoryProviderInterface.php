<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

interface GoogleCategoryProviderInterface
{
    /**
     * @param int $categoryId
     *
     * @return array|null
     */
    public function getGoogleCategory($categoryId);

    /**
     * @param $categoryId
     * @param $page
     *
     * @return array|null
     */
    public function getGoogleCategoryChildes($categoryId, $page);
}
