<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

class GoogleProduct
{
    const POSITION_PRODUCT_ID = 0;
    const POSITION_PRODUCT_ATTRIBUTE_ID = 1;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $productAttributeId;

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     *
     * @return GoogleProduct
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * @return int
     */
    public function getProductAttributeId()
    {
        return $this->productAttributeId;
    }

    /**
     * @param int $productAttributeId
     *
     * @return GoogleProduct
     */
    public function setProductAttributeId($productAttributeId)
    {
        $this->productAttributeId = $productAttributeId;

        return $this;
    }
}