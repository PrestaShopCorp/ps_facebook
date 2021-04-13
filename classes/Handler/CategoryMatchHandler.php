<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

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
     * @param int|null $googleCategoryId
     * @param string $googleCategoryName
     * @param int $googleCategoryParentId
     * @param string $googleCategoryParentName
     * @param bool $updateChildren
     * @param int $shopId
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updateCategoryMatch(
        $categoryId,
        $googleCategoryId,
        $googleCategoryName,
        $googleCategoryParentId,
        $googleCategoryParentName,
        $updateChildren,
        $shopId
    ) {
        if ($googleCategoryId === null) {
            return $this->unsetCategoryMatch($categoryId, $updateChildren, $shopId);
        }

        if ($updateChildren === true) {
            $category = new Category($categoryId);
            $categoryChildrenIds = $category->getAllChildren();

            $this->googleCategoryRepository->updateCategoryChildrenMatch(
                $categoryChildrenIds,
                $googleCategoryId,
                $googleCategoryName,
                $googleCategoryParentId,
                $googleCategoryParentName,
                $shopId
            );
        }

        $this->googleCategoryRepository->updateCategoryMatch(
            $categoryId,
            $googleCategoryId,
            $googleCategoryName,
            $googleCategoryParentId,
            $googleCategoryParentName,
            $shopId,
            $updateChildren
        );
    }

    /**
     * @param int $categoryId
     * @param bool $updateChildren
     * @param int $shopId
     *
     * @throws \PrestaShopDatabaseException
     */
    public function unsetCategoryMatch(
        $categoryId,
        $updateChildren,
        $shopId
    ) {
        if ($updateChildren === true) {
            $category = new Category($categoryId);
            $categoryChildrenIds = $category->getAllChildren();

            $this->googleCategoryRepository->unsetCategoryChildrenMatch(
                $categoryChildrenIds,
                $shopId
            );
        }
        $this->googleCategoryRepository->unsetCategoryMatch(
            $categoryId,
            $shopId
        );
    }
}
