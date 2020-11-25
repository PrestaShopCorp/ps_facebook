<?php

namespace PrestaShop\Module\PrestashopFacebook\Adapter;

use Configuration;
use Shop;

class ConfigurationAdapter
{
    /**
     * @var Shop
     */
    private $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    public function get($key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        if ($idShop === null) {
            $idShop = $this->shop->id;
        }

        return Configuration::get($key, $idLang, $idShopGroup, $idShop, $default);
    }

    public function updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        if ($idShop === null) {
            $idShop = $this->shop->id;
        }

        return Configuration::updateValue($key, $values, $html, $idShopGroup, $idShop);
    }

    public function deleteByName($key)
    {
        return Configuration::deleteByName($key);
    }
}
