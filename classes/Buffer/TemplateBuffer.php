<?php

namespace PrestaShop\Module\PrestashopFacebook\Buffer;

class TemplateBuffer
{
    private $data;

    public function add(string $data): void
    {
        $this->data .= $data;
    }

    public function clean(): void
    {
        $this->data = '';
    }

    public function flush(): string
    {
        $returnedData = $this->data;
        $this->clean();

        return !empty($returnedData) ? $returnedData : '';
    }
}
