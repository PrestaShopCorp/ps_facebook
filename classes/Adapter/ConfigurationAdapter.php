<?php

namespace PrestaShop\Module\PrestashopFacebook\Adapter;

use Configuration;

class ConfigurationAdapter
{
    public function get($id)
    {
        return Configuration::get($id);
    }
}
