<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler\Pixel;

use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;

class BaseEvent
{
    protected $templateBuffer;

    public function __construct(TemplateBuffer $tplBuffer)
    {
        $this->templateBuffer = $tplBuffer;
    }
}
