<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler\Pixel;

class SearchEvent extends BaseEvent
{
    public function send($event)
    {
        $this->templateBuffer->add('');
    }
}
