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

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShopCollection;

class GoogleCategoryRepository
{
    public const NO_CHILDREN = 0;
    public const HAS_CHILDREN = 1;

    /**
     * @var int
     */
    private $homeCategoryId;

    public function __construct(ConfigurationAdapter $configurationAdapter)
    {
        $this->homeCategoryId = (int) $configurationAdapter->get('PS_HOME_CATEGORY');
    }

    /**
     * @param int $categoryId
     * @param int $googleCategoryId
     * @param string $googleCategoryName
     * @param int $googleCategoryParentId
     * @param string $googleCategoryParentName
     * @param int $shopId
     * @param bool $isParentCategory
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updateCategoryMatch(
        $categoryId,
        $googleCategoryId,
        $googleCategoryName,
        $googleCategoryParentId,
        $googleCategoryParentName,
        $shopId,
        $isParentCategory = false
    ) {
        Db::getInstance()->insert(
            'fb_category_match',
            [
                'id_category' => (int) $categoryId,
                'google_category_id' => (int) $googleCategoryId,
                'google_category_name' => pSQL($googleCategoryName),
                'google_category_parent_id' => (int) $googleCategoryParentId,
                'google_category_parent_name' => pSQL($googleCategoryParentName),
                'is_parent_category' => (bool) $isParentCategory,
                'id_shop' => (int) $shopId,
            ],
            false,
            true,
            DB::REPLACE
        );
    }

    /**
     * @param PrestaShopCollection $childCategories
     * @param int $googleCategoryId
     * @param string $googleCategoryName
     * @param int $googleCategoryParentId
     * @param string $googleCategoryParentName
     * @param int $shopId
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updateCategoryChildrenMatch(
        PrestaShopCollection $childCategories,
        $googleCategoryId,
        $googleCategoryName,
        $googleCategoryParentId,
        $googleCategoryParentName,
        $shopId
    ) {
        $data = [];

        foreach ($childCategories as $category) {
            $data[] = [
                'id_category' => (int) $category->id,
                'google_category_id' => (int) $googleCategoryId,
                'google_category_name' => pSQL($googleCategoryName),
                'google_category_parent_id' => (int) $googleCategoryParentId,
                'google_category_parent_name' => pSQL($googleCategoryParentName),
                'is_parent_category' => 1,
                'id_shop' => (int) $shopId,
            ];
        }

        Db::getInstance()->insert(
            'fb_category_match',
            $data,
            false,
            true,
            DB::REPLACE
        );
    }

    /**
     * @param int $categoryId
     * @param int $shopId
     *
     * @throws \PrestaShopDatabaseException
     */
    public function unsetCategoryMatch(
        $categoryId,
        $shopId
    ) {
        Db::getInstance()->delete(
            'fb_category_match',
            '`id_category` = ' . (int) $categoryId . ' AND `id_shop` = ' . (int) $shopId
        );
    }

    /**
     * @param PrestaShopCollection $childCategories
     * @param int $shopId
     *
     * @throws \PrestaShopDatabaseException
     */
    public function unsetCategoryChildrenMatch(
        PrestaShopCollection $childCategories,
        $shopId
    ) {
        foreach ($childCategories as $category) {
            $this->unsetCategoryMatch($category->id, $shopId);
        }
    }

    /**
     * @param int $categoryId
     * @param int $shopId
     *
     * @return int
     */
    public function getGoogleCategoryIdByCategoryId($categoryId, $shopId)
    {
        $sql = new DbQuery();
        $sql->select('google_category_id');
        $sql->from('fb_category_match');
        $sql->where('`id_category` = "' . (int) $categoryId . '"');
        $sql->where('id_shop = ' . (int) $shopId);

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * @param int $categoryId
     * @param int $shopId
     *
     * @return array|false
     */
    public function getCategoryMatchByCategoryId($categoryId, $shopId)
    {
        $sql = new DbQuery();
        $sql->select('id_category');
        $sql->select('google_category_id');
        $sql->select('google_category_name');
        $sql->select('google_category_parent_id');
        $sql->select('google_category_parent_name');
        $sql->select('is_parent_category');
        $sql->from('fb_category_match');
        $sql->where('`id_category` = "' . (int) $categoryId . '"');
        $sql->where('id_shop = ' . (int) $shopId);

        return Db::getInstance()->getRow($sql);
    }

    /**
     * @param array $categoryIds
     * @param int $shopId
     *
     * @return array|false
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getGoogleCategoryIdsByCategoryIds(array $categoryIds, $shopId)
    {
        $sql = new DbQuery();
        $sql->select('google_category_id');
        $sql->from('fb_category_match');
        $sql->where('`id_category` IN ("' . implode('", "', array_map('intval', $categoryIds)) . '")');
        $sql->where('id_shop = ' . (int) $shopId);

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param array $categoryIds
     * @param int $shopId
     *
     * @return array|false
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getCategoryMatchesByCategoryIds(array $categoryIds, $shopId)
    {
        $sql = new DbQuery();
        $sql->select('id_category');
        $sql->select('google_category_id');
        $sql->select('google_category_parent_id');
        $sql->select('is_parent_category');
        $sql->from('fb_category_match');
        $sql->where('`id_category` IN ("' . implode('", "', array_map('intval', $categoryIds)) . '")');
        $sql->where('id_shop = ' . (int) $shopId);

        return Db::getInstance()->executeS($sql);
    }

    public function getFilteredCategories($parentCategoryId, $langId, $offset, $limit, $shopId)
    {
        $sql = new DbQuery();
        $sql->select('c.id_category as shopCategoryId');
        $sql->select('cl.name as shopCategoryName');
        $sql->select('cm.google_category_id as googleCategoryId');
        $sql->select('cm.google_category_name as googleCategoryName');
        $sql->select('cm.google_category_parent_id as googleCategoryParentId');
        $sql->select('cm.google_category_parent_name as googleCategoryParentName');
        $sql->select('cm.is_parent_category as isParentCategory');
        $sql->select('case when c.nleft + 1 = c.nright and c.`level_depth` = ' . Config::MAX_CATEGORY_DEPTH .
            ' then ' . self::NO_CHILDREN . ' else ' . self::HAS_CHILDREN . ' end deploy');
        $sql->from('category', 'c');
        $sql->innerJoin('category_shop', 'cs', 'cs.id_category = c.id_category');
        $sql->innerJoin('category_lang', 'cl', 'c.id_category = cl.id_category AND cl.id_lang = ' . (int) $langId);
        $sql->leftJoin(
            'fb_category_match',
            'cm',
            'cm.id_category = c.id_category AND cm.id_shop = ' . (int) $shopId
        );
        $sql->where('cs.id_shop = ' . (int) $shopId);
        $sql->where(
            'c.`id_parent` = ' . (int) $parentCategoryId . ' OR
            (
                        c.`nleft` > (SELECT pc.`nleft` from `' . _DB_PREFIX_ . 'category` as pc WHERE pc.id_category = '
            . (int) $parentCategoryId . ' AND pc.`level_depth` >= ' . Config::MAX_CATEGORY_DEPTH . ') AND
                        c.`nright` < (SELECT pc.`nright` from `' . _DB_PREFIX_ . 'category` as pc WHERE pc.id_category = '
            . (int) $parentCategoryId . ' AND pc.`level_depth` >= ' . Config::MAX_CATEGORY_DEPTH . ')
            )'
        );
        $sql->groupBy('c.id_category');
        $sql->limit($limit, $offset);

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param int $shopId
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    public function areParentCategoriesMatched($shopId)
    {
        $sql = new DbQuery();
        $sql->select('c.id_category');
        $sql->from('category', 'c');
        $sql->innerJoin('category_shop', 'cs', 'cs.id_category = c.id_category');
        $sql->leftJoin('fb_category_match', 'cm', 'cm.id_category = c.id_category AND cm.id_shop = cs.id_shop');
        $sql->where('c.id_parent = ' . (int) $this->homeCategoryId . ' AND cm.google_category_id IS NULL');
        $sql->where('cs.id_shop = ' . (int) $shopId);

        return (bool) Db::getInstance()->executeS($sql);
    }

    /**
     * @param int $langId
     * @param int $shopId
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getCategoriesWithParentInfo($langId, $shopId)
    {
        $query = new DbQuery();
        $query->select('c.id_category, cl.name, c.id_parent')
            ->from('category', 'c')
            ->leftJoin(
                'category_lang',
                'cl',
                'cl.id_category = c.id_category AND cl.id_shop = ' . (int) $shopId
            )
            ->where('cl.id_lang = ' . (int) $langId)
            ->orderBy('cl.id_category');
        $result = Db::getInstance()->executeS($query);
        if ($result) {
            return $result;
        } else {
            throw new \PrestaShopDatabaseException('No categories found');
        }
    }

    /**
     * @param int $shopId
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    public function isMatchingDone($shopId)
    {
        $sql = new DbQuery();
        $sql->select('cm.id_category');
        $sql->from('fb_category_match', 'cm');
        $sql->where('cm.id_shop = ' . (int) $shopId);

        return (bool) Db::getInstance()->executeS($sql);
    }

    /**
     * @param int $shopId
     *
     * @return int
     */
    public function getNumberOfMatchedCategories($shopId)
    {
        $sql = new DbQuery();
        $sql->select('cm.id_category');
        $sql->from('fb_category_match', 'cm');
        $sql->where('cm.id_shop = ' . (int) $shopId);

        Db::getInstance()->execute($sql);

        return Db::getInstance()->numRows();
    }

    /**
     * @param int $shopId
     *
     * @return int
     */
    public function getNumberOfTotalCategories($shopId)
    {
        $sql = new DbQuery();
        $sql->select('c.id_category');
        $sql->from('category', 'c');
        $sql->innerJoin('category_shop', 'cp', 'cp.id_category = c.id_category');
        $sql->where('cp.id_shop = ' . (int) $shopId . ' AND c.id_parent >=' . (int) $this->homeCategoryId);

        Db::getInstance()->execute($sql);

        return Db::getInstance()->numRows();
    }
}
