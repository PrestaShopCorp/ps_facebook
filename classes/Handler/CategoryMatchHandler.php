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
     * @param int $shopId
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updateCategoryMatch($categoryId, $googleCategoryId, $updateChildren, $shopId)
    {
        if ($updateChildren) {
            $category = new Category($categoryId);
            $categoryChildrenIds = $category->getAllChildren();
            $this->googleCategoryRepository->updateCategoryChildrenMatch($categoryChildrenIds, $googleCategoryId, $shopId);
        }
        $this->googleCategoryRepository->updateCategoryMatch($categoryId, $googleCategoryId, $shopId, $updateChildren);
    }
}
