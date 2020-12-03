<?php

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;
use PrestaShopException;

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

    public function getProductsWithErrors($shopId)
    {
        $sql = new DbQuery();

        $sql->select('ps.id_product, pas.id_product_attribute, pl.name');
        $sql->select('pl.id_lang');
        $sql->select('
            IF((m.name = "" OR m.name IS NULL) AND p.ean13 = "" AND p.upc = "" AND p.isbn = "", false, true) as has_manufacturer_or_ean_or_upc_or_isbn
            ');
        $sql->select('IF(is.id_image IS NOT NULL, true, false) as has_cover');
        $sql->select('IF(pl.link_rewrite = "" OR pl.link_rewrite is NULL, false, true) as has_link');
        $sql->select('IF(ps.price > 0, true, false) as has_price_tax_excl');
        $sql->select('IF((pl.description_short = "" OR pl.description_short IS NULL) AND (pl.description = "" OR pl.description IS NULL), false, true) as has_description_or_short_description');

        $sql->from('product', 'p');

        $sql->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product');
        $sql->innerJoin('product_lang', 'pl', 'pl.id_product = ps.id_product AND pl.id_shop = ps.id_shop');
        $sql->leftJoin('product_attribute_shop', 'pas', 'pas.id_product = ps.id_product AND pas.id_shop = ps.id_shop');
        $sql->leftJoin('manufacturer', 'm', 'm.id_manufacturer = p.id_manufacturer');
        $sql->leftJoin('image_shop', 'is', 'is.id_product = ps.id_product AND is.id_shop = ps.id_shop AND is.cover = 1');

        $sql->where('ps.id_shop = ' . (int) $shopId);
        $sql->where('
        (m.name = "" OR m.name IS NULL) AND p.ean13 = "" AND p.upc = "" AND p.isbn = ""
        OR ((pl.description_short = "" OR pl.description_short IS NULL) AND (pl.description = "" OR pl.description IS NULL))
        OR is.id_image is NULL
        OR pl.link_rewrite = "" OR pl.link_rewrite is NULL
        OR ps.price = 0 
        OR pl.name = "" OR pl.name is NULL
        ');

        return Db::getInstance()->executeS($sql);
    }
}
