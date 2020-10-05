<?php

namespace PrestaShop\Module\PrestashopFacebook\Repository;

use Db;
use DbQuery;

class TabRepository
{
    public function hasChildren($tabId)
    {
        $sql = new DbQuery();
        $sql->select('id_tab');
        $sql->from('tab');
        $sql->where('`id_parent` = "' . (int) $tabId . '"');

        return (bool) Db::getInstance()->getValue($sql);
    }
}
