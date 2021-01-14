<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

class GoogleProduct
{
    const POSITION_PRODUCT_ID = 0;
    const POSITION_PRODUCT_ATTRIBUTE_ID = 1;
    const POSITION_COUNTRY_ISO_CODE = 2;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $productAttributeId;

    /**
     * @var string
     */
    private $landIsoCode;

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

    /**
     * @return string
     */
    public function getLandIsoCode()
    {
        return $this->landIsoCode;
    }

    /**
     * @param string $landIsoCode
     *
     * @return GoogleProduct
     */
    public function setLandIsoCode($landIsoCode)
    {
        $this->landIsoCode = $landIsoCode;

        return $this;
    }
}
