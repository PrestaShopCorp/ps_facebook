<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;

class GoogleCategoryProvider implements GoogleCategoryProviderInterface
{
    /**
     * @var GoogleCategoryRepository
     */
    private $googleCategoryRepository;

    public function __construct(GoogleCategoryRepository $googleCategoryRepository)
    {
        $this->googleCategoryRepository = $googleCategoryRepository;
    }

    /**
     * @param int $categoryId
     *
     * @return array|false
     */
    public function getGoogleCategory($categoryId)
    {
        return $this->googleCategoryRepository->getGoogleCategoryByCategoryId($categoryId);
    }
}
