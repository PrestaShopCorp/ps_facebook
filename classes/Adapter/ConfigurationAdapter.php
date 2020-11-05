<?php

namespace PrestaShop\Module\PrestashopFacebook\Adapter;

use Configuration;

class ConfigurationAdapter
{
    public function get($id)
    {
        return Configuration::get($id);
    }

    public function updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        return Configuration::updateValue($key, $values, $html, $idShopGroup, $idShop);
    }

    public function deleteByName($key)
    {
        return Configuration::deleteByName($key);
    }
}
