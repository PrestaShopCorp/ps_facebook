<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;
use Shop;

class GoogleCategoryProvider implements GoogleCategoryProviderInterface
{
    /**
     * @var GoogleCategoryRepository
     */
    private $googleCategoryRepository;

    /**
     * @var Shop
     */
    private $shop;

    public function __construct(
        GoogleCategoryRepository $googleCategoryRepository,
        Shop $shop
    ) {
        $this->googleCategoryRepository = $googleCategoryRepository;
        $this->shop = $shop;
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
     * @param int $page
     *
     * @return array|null
     */
    public function getGoogleCategoryChildren($categoryId, $langId, $page = 1)
    {
        if ($page < 1) {
            $page = 1;
        }
        $googleCategories = $this->googleCategoryRepository->getFilteredCategories(
            $categoryId,
            $langId,
            Config::CATEGORIES_PER_PAGE * ($page - 1),
            Config::CATEGORIES_PER_PAGE,
            $this->shop->id
        );

        if (!is_array($googleCategories)) {
            return null;
        }

        return $googleCategories;
    }
}
