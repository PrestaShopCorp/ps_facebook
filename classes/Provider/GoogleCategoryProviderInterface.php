<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

interface GoogleCategoryProviderInterface
{
    /**
     * @param int $categoryId
     *
     * @return string|null
     */
    public function getGoogleCategory($categoryId);
}
