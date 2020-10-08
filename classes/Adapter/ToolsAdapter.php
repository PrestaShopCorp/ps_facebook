<?php

namespace PrestaShop\Module\PrestashopFacebook\Adapter;

use Tools;

class ToolsAdapter
{
    public function getValue($id)
    {
        return Tools::getValue($id);
    }

    public function isSubmit($id)
    {
        return Tools::isSubmit($id);
    }
}
