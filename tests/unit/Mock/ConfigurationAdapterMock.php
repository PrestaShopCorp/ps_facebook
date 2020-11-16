<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Mock;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;

class ConfigurationAdapterMock extends ConfigurationAdapter
{
    /**
     * @var array
     */
    private $data = [];

    public function setFakeData(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    public function get($id)
    {
        return isset($this->data[$id]) ? $this->data : false;
    }

    public function updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        // Simple registration, we don't take care about multi lang values etc.
        $this->data[$key] = $values;

        return true;
    }
}
