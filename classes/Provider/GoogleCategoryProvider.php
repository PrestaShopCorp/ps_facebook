<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;

class GoogleCategoryProvider implements GoogleCategoryProviderInterface
{
    /**
     * @var GoogleCategoryRepository
     */
    private $googleCategoryRepository;

    public function __construct(
        GoogleCategoryRepository $googleCategoryRepository
    ) {
        $this->googleCategoryRepository = $googleCategoryRepository;
    }

    /**
     * @param int $categoryId
     *
     * @return array|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getGoogleCategory($categoryId)
    {
        $categoryMatch = $this->googleCategoryRepository->getCategoryMatchByCategoryId($categoryId);
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
        $googleCategory = $this->googleCategoryRepository->getFilteredCategories(
            $categoryId,
            $langId,
            Config::CATEGORIES_PER_PAGE * ($page - 1),
            Config::CATEGORIES_PER_PAGE
        );

        if (!is_array($googleCategory)) {
            return null;
        }

        return $googleCategory;
    }
}
