<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Category;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;

class CategoryMatchHandler
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
     * @param int $googleCategoryId
     * @param bool $updateChildren
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updateCategoryMatch($categoryId, $googleCategoryId, $updateChildren)
    {
        if ($updateChildren) {
            $category = new Category($categoryId);
            $categoryChildrenIds = $category->getAllChildren();
            $this->googleCategoryRepository->updateCategoryChildrenMatch($categoryChildrenIds, $googleCategoryId);
        }
        $this->googleCategoryRepository->updateCategoryMatch($categoryId, $googleCategoryId);
    }
}
