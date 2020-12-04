<?php

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class ShopRepository
{
    public function getShopDomainsAndConfiguration()
    {
        $sql = new DbQuery();

        $sql->select('su.`id_shop`, `domain`, `domain_ssl`, c.`value` as acces_token_value');

        $sql->from('shop_url', 'su');
        $sql->leftJoin('configuration', 'c', 'su.id_shop = c.id_shop');

        $sql->where('c.name LIKE "' . Config::PS_FACEBOOK_USER_ACCESS_TOKEN . '"');

        return Db::getInstance()->executeS($sql);
    }
}
