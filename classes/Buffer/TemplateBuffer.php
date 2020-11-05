<?php

namespace PrestaShop\Module\PrestashopFacebook\Buffer;

class TemplateBuffer
{
    /**
     * @var string
     */
    private $data;

    /**
     * add data to the buffer
     *
     * @param string $data
     *
     * @return void
     */
    public function add($data)
    {
        $this->data .= $data;
    }

    /**
     * reset buffer content
     *
     * @return void
     */
    public function clean()
    {
        $this->data = '';
    }

    /**
     * return buffer content and reset it
     *
     * @return string
     */
    public function flush()
    {
        $returnedData = $this->data;
        $this->clean();

        return !empty($returnedData) ? $returnedData : '';
    }
}
