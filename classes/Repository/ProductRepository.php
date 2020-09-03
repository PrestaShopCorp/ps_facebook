<?php

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;

class ProductRepository
{
    /**
     * @param int $langId
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getProductsForFacebook($langId)
    {
        $sql = new DbQuery();
        $sql->select('p.*');
        $sql->select('pl.name as `product_name`, pl.description_short as `product_description_short`');
        $sql->select('m.name as `manufacturer_name`');
        $sql->innerJoin('product_lang', 'pl', 'pl.id_product = p.id_product AND pl.id_lang = ' . (int) $langId);
        $sql->leftJoin('manufacturer', 'm', 'm.id_manufacturer = p.id_manufacturer');
        $sql->from('product', 'p');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}
