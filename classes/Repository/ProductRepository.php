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
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\DTO\EventBusProduct;
use PrestaShop\Module\Ps_facebook\Utility\ProductCatalogUtility;
use PrestaShopException;
use Product;
use Validate;

class ProductRepository
{
    /**
     * Copy of prestashop Product::getIdProductAttributeByIdAttributes function
     * because old PS versions are missing this function
     *
     * Get an id_product_attribute by an id_product and one or more
     * id_attribute.
     *
     * e.g: id_product 8 with id_attribute 4 (size medium) and
     * id_attribute 5 (color blue) returns id_product_attribute 9 which
     * is the dress size medium and color blue.
     *
     * @param int $idProduct
     * @param int|int[] $idAttributes
     * @param bool $findBest
     *
     * @return int
     *
     * @throws PrestaShopException
     */
    public function getIdProductAttributeByIdAttributes($idProduct, $idAttributes, $findBest = false)
    {
        $idProduct = (int) $idProduct;

        if (!is_array($idAttributes) && is_numeric($idAttributes)) {
            $idAttributes = [(int) $idAttributes];
        }

        if (!is_array($idAttributes) || empty($idAttributes)) {
            throw new PrestaShopException(sprintf('Invalid parameter $idAttributes with value: "%s"', print_r($idAttributes, true)));
        }

        $idAttributesImploded = implode(',', array_map('intval', $idAttributes));
        $idProductAttribute = Db::getInstance()->getValue(
            '
            SELECT
                pac.`id_product_attribute`
            FROM
                `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
            WHERE
                pa.id_product = ' . $idProduct . '
                AND pac.id_attribute IN (' . $idAttributesImploded . ')
            GROUP BY
                pac.`id_product_attribute`
            HAVING
                COUNT(pa.id_product) = ' . count($idAttributes)
        );

        if ($idProductAttribute === false && $findBest) {
            //find the best possible combination
            //first we order $idAttributes by the group position
            $orderred = [];
            $result = Db::getInstance()->executeS(
                '
                SELECT
                    a.`id_attribute`
                FROM
                    `' . _DB_PREFIX_ . 'attribute` a
                    INNER JOIN `' . _DB_PREFIX_ . 'attribute_group` g ON a.`id_attribute_group` = g.`id_attribute_group`
                WHERE
                    a.`id_attribute` IN (' . $idAttributesImploded . ')
                ORDER BY
                    g.`position` ASC'
            );

            foreach ($result as $row) {
                $orderred[] = $row['id_attribute'];
            }

            while ($idProductAttribute === false && count($orderred) > 0) {
                array_pop($orderred);
                $idProductAttribute = Db::getInstance()->getValue(
                    '
                    SELECT
                        pac.`id_product_attribute`
                    FROM
                        `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                        INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                    WHERE
                        pa.id_product = ' . (int) $idProduct . '
                        AND pac.id_attribute IN (' . implode(',', array_map('intval', $orderred)) . ')
                    GROUP BY
                        pac.id_product_attribute
                    HAVING
                        COUNT(pa.id_product) = ' . count($orderred)
                );
            }
        }

        if (empty($idProductAttribute)) {
            throw new PrestaShopException('Can not retrieve the id_product_attribute');
        }

        return (int) $idProductAttribute;
    }

    public function getProductsWithErrors($shopId, $page = -1)
    {
        $sql = new DbQuery();
        $sql->select('ps.id_product');
        $sql->select('IF(pas.id_product_attribute IS NULL, 0, id_product_attribute) as id_product_attribute');
        $sql->from('product', 'p');
        $sql->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product');
        $sql->leftJoin('product_attribute_shop', 'pas', 'pas.id_product = ps.id_product AND pas.id_shop = ps.id_shop');
        $sql->where('ps.id_shop = ' . (int) $shopId . ' AND ps.active = 1');

        $result = Db::getInstance()->executeS($sql);
        $productIdsWithInvalidSalePrice = [0];
        $productAttributeIdsWithInvalidSalePrice = [0];
        foreach ($result as $product) {
            $salePriceTaxExcluded = $this->getSalePriceTaxExcluded($product['id_product'], $product['id_product_attribute']);
            if ($salePriceTaxExcluded <= 0) {
                $productIdsWithInvalidSalePrice[] = $product['id_product'];
                $productAttributeIdsWithInvalidSalePrice[] = $product['id_product_attribute'];
            }
        }

        $sql = new DbQuery();

        $sql->select('ps.id_product, pas.id_product_attribute, pl.name');
        $sql->select('pl.id_lang, l.iso_code as language');
        $sql->select('
            IF((m.name = "" OR m.name IS NULL) AND p.ean13 = "" AND p.upc = "" AND p.isbn = "", false, true) as has_manufacturer_or_ean_or_upc_or_isbn
            ');
        $sql->select('IF(is.id_image IS NOT NULL, true, false) as has_cover');
        $sql->select('IF(pl.link_rewrite = "" OR pl.link_rewrite is NULL, false, true) as has_link');
        $sql->select('IF(ps.price + IFNULL(pas.price, 0) > 0, true, false) as has_price_tax_excl');
        $sql->select('IF((pl.description_short = "" OR pl.description_short IS NULL) AND (pl.description = "" OR pl.description IS NULL), false, true) as has_description_or_short_description');
        $sql->select('true as correct_sales_price');

        $sql->from('product', 'p');

        $sql->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product');
        $sql->innerJoin('product_lang', 'pl', 'pl.id_product = ps.id_product AND pl.id_shop = ps.id_shop');
        $sql->innerJoin('lang', 'l', 'l.id_lang = pl.id_lang');
        $sql->leftJoin('product_attribute_shop', 'pas', 'pas.id_product = ps.id_product AND pas.id_shop = ps.id_shop');
        $sql->leftJoin('manufacturer', 'm', 'm.id_manufacturer = p.id_manufacturer');
        $sql->leftJoin('image_shop', 'is', 'is.id_product = ps.id_product AND is.id_shop = ps.id_shop AND is.cover = 1');

        $sql->where('ps.id_shop = ' . (int) $shopId . ' AND ps.active = 1');
        $sql->where('
        (m.name = "" OR m.name IS NULL) AND p.ean13 = "" AND p.upc = "" AND p.isbn = ""
        OR ((pl.description_short = "" OR pl.description_short IS NULL) AND (pl.description = "" OR pl.description IS NULL))
        OR is.id_image is NULL
        OR pl.link_rewrite = "" OR pl.link_rewrite is NULL
        OR ps.price + IFNULL(pas.price, 0) <= 0
        OR pl.name = "" OR pl.name is NULL
        OR
        (
            ps.id_product in (' . implode(',', $productIdsWithInvalidSalePrice) . ') AND
            (
                pas.id_product_attribute in (' . implode(',', $productAttributeIdsWithInvalidSalePrice) . ') OR
                pas.id_product_attribute IS NULL
            )
        )
        ');

        $sql->orderBy('p.id_product ASC, pas.id_product_attribute ASC, language ASC');

        if ($page > -1) {
            $sql->limit(Config::REPORTS_PER_PAGE, Config::REPORTS_PER_PAGE * ($page));
        }

        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $key => &$value) {
            if (!in_array($value['id_product'], $productIdsWithInvalidSalePrice)) {
                continue;
            }
            if (in_array($value['id_product_attribute'], $productAttributeIdsWithInvalidSalePrice)
                || $value['id_product_attribute'] === null) {
                $value['correct_sales_price'] = false;
            }
        }

        return $result;
    }

    public function getProductsTotal($shopId, array $options = [])
    {
        $sql = new DbQuery();

        $sql->select('COUNT(1) as total');
        $sql->from('product', 'p');

        $sql->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product');
        $sql->innerJoin('product_lang', 'pl', 'pl.id_product = ps.id_product AND pl.id_shop = ps.id_shop');
        $sql->leftJoin('product_attribute_shop', 'pas', 'pas.id_product = ps.id_product AND pas.id_shop = ps.id_shop');

        $sql->where('ps.id_shop = ' . (int) $shopId);
        if (isset($options['onlyActive'])) {
            $sql->where('ps.active = 1');
        }

        $res = Db::getInstance()->executeS($sql);

        return $res[0]['total'];
    }

    /**
     * @param EventBusProduct $eventBusProduct
     * @param int $shopId
     * @param string $isoCode
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getInformationAboutEventBusProduct(EventBusProduct $eventBusProduct, $shopId, $isoCode)
    {
        $sql = new DbQuery();

        $sql->select('pa.id_product, pa.id_product_attribute, pl.name');
        $sql->select('l.iso_code');

        $sql->from('product_attribute', 'pa');
        $sql->innerJoin('lang', 'l', 'l.iso_code = "' . pSQL($isoCode) . '"');
        $sql->innerJoin('product_lang', 'pl', 'pl.id_product = pa.id_product AND pl.id_lang = l.id_lang');

        $sql->where('pa.id_product = ' . (int) $eventBusProduct->getProductId());
        $sql->where('pa.id_product_attribute = ' . (int) $eventBusProduct->getProductAttributeId());
        $sql->where('pl.id_shop = ' . (int) $shopId);

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param string $syncUpdateDate
     * @param int $shopId
     * @param array $productsWithErrors
     * @param int|false $page
     * @param string|null $status
     * @param string|null $sortBy
     * @param string $sortTo
     * @param int|bool $searchById
     * @param string|bool $searchByName
     * @param string|bool $searchByMessage
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getInformationAboutEventBusProducts(
        $syncUpdateDate,
        $shopId,
        $productsWithErrors,
        $page = false,
        $status = null,
        $sortBy = null,
        $sortTo = 'ASC',
        $searchById = false,
        $searchByName = false,
        $searchByMessage = false
    ) {
        $sql = new DbQuery();

        $sql->select('ps.id_product, pa.id_product_attribute, pl.name, ps.date_upd');
        $sql->select('
            IF(CONCAT_WS("-", ps.id_product, IFNULL(pa.id_product_attribute, "0")) IN ( "' . implode('","', array_map('pSQL', $productsWithErrors)) . '"), "disapproved",
            IF(ps.date_upd <= "' . pSQL($syncUpdateDate) . '", "approved", "pending" )
             ) as status
        ');

        $sql->from('product_shop', 'ps');
        $sql->leftJoin('product_attribute', 'pa', 'pa.id_product = ps.id_product');
        $sql->innerJoin('product_lang', 'pl', 'pl.id_product = ps.id_product');

        $sql->where('pl.id_shop = ' . (int) $shopId);
        $sql->where('CONCAT_WS("-", ps.id_product, IFNULL(pa.id_product_attribute, 0)) IN ( "' . implode('","', array_map('pSQL', $productsWithErrors)) . '")');

        // GROUP BY Id product AND ID combination of attributes
        $sql->groupBy('ps.id_product');
        $sql->groupBy('pa.id_product_attribute');

        if ($page !== false) {
            $sql->limit(Config::REPORTS_PER_PAGE, Config::REPORTS_PER_PAGE * ($page - 1));
        }

        if ($sortBy && Validate::isOrderBy($sortBy) && Validate::isOrderWay($sortTo)) {
            $sql->orderBy(pSQL($sortBy) . ' ' . pSQL($sortTo));
        }
        if ($searchById) {
            $sql->where('ps.id_product LIKE "%' . (int) $searchById . '%"');
        }
        if ($searchByName) {
            $sql->where('pl.name LIKE "%' . pSQL($searchByName) . '%"');
        }
        if ($searchByMessage) {
            $sql->where('ps.id_product LIKE "%' . (int) $searchByMessage . '%"');
        }
        if ($status) {
            $sql->having('ps.id_product LIKE "%' . pSQL($status) . '%"');
        }

        $result = Db::getInstance()->executeS($sql);
        $products = [];
        foreach ($result as $product) {
            $eventBusProductId = ProductCatalogUtility::makeProductId(
                $product['id_product'],
                $product['id_product_attribute']
            );
            $products[$eventBusProductId] = $product;
        }

        return $products;
    }

    /**
     * @param int $productId
     * @param int $attributeId
     *
     * @return float
     */
    public function getSalePriceTaxExcluded($productId, $attributeId)
    {
        return Product::getPriceStatic($productId, false, $attributeId, 6);
    }

    /**
     * @param int $productId
     * @param int $attributeId
     * @param int|null $id_cart Needed to avoid a Fatal Error from PrestaShop
     *
     * @return float
     */
    public function getSalePrice($productId, $attributeId, $id_cart = null)
    {
        return Product::getPriceStatic(
            $productId,
            true,
            $attributeId,
            6,
            null,
            false,
            true,
            1,
            false,
            null,
            $id_cart
        );
    }
}
