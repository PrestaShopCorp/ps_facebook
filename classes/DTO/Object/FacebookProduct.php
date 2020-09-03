<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO\Object;

use JsonSerializable;

class FacebookProduct implements JsonSerializable
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @return string
     */
    private $description;

    /**
     * @return string
     */
    private $availability;

    /**
     * @return int
     */
    private $inventory;

    /**
     * @return string
     */
    private $condition;

    /**
     * @return string
     */
    private $price;

    /**
     * @return string
     */
    private $link;

    /**
     * @var string
     */
    private $imageLink;

    /**
     * @var string
     */
    private $brand;

    /**
     * @var string
     */
    private $additionalImageLink;

    /**
     * @var string
     */
    private $ageGroup;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $itemGroupId;

    /**
     * @var string
     */
    private $googleProductCategory;

    /**
     * @var string
     */
    private $commerceTaxCategory;

    /**
     * @var string
     */
    private $material;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $productType;

    /**
     * @var string
     */
    private $salePrice;

    /**
     * Type: two ISO-8601 timestamp
     *
     * @var string
     */
    private $salePriceEffectiveDate;

    /**
     * @var string
     */
    private $shipping;

    /**
     * @var string
     */
    private $shippingWeight;

    /**
     * @var string
     */
    private $size;

    /**
     * @var string
     */
    private $customLabel0;

    /**
     * @var string
     */
    private $customLabel1;

    /**
     * @var string
     */
    private $customLabel2;

    /**
     * @var string
     */
    private $customLabel3;

    /**
     * @var string
     */
    private $customLabel4;

    /**
     * @var string
     */
    private $richTextDescription;

    /**
     * @var string
     */
    private $gtin;

    /**
     * @var string
     */
    private $mpn;

    /**
     * @var string
     */
    private $returnPolicyInfo;

    /**
     * @var string
     */
    private $launchDate;

    /**
     * @var string
     */
    private $expirationDate;

    /**
     * @var string
     */
    private $visibility;

    /**
     * @var string
     */
    private $mobileLink;

    /**
     * @var string
     */
    private $additionalVariantAttribute;

    /**
     * @var string
     */
    private $appLink;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return FacebookProduct
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return FacebookProduct
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return FacebookProduct
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param mixed $availability
     * @return FacebookProduct
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param mixed $inventory
     * @return FacebookProduct
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param mixed $condition
     * @return FacebookProduct
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return FacebookProduct
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     * @return FacebookProduct
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @param string $imageLink
     * @return FacebookProduct
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     * @return FacebookProduct
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalImageLink()
    {
        return $this->additionalImageLink;
    }

    /**
     * @param string $additionalImageLink
     * @return FacebookProduct
     */
    public function setAdditionalImageLink($additionalImageLink)
    {
        $this->additionalImageLink = $additionalImageLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getAgeGroup()
    {
        return $this->ageGroup;
    }

    /**
     * @param string $ageGroup
     * @return FacebookProduct
     */
    public function setAgeGroup($ageGroup)
    {
        $this->ageGroup = $ageGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return FacebookProduct
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     * @return FacebookProduct
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemGroupId()
    {
        return $this->itemGroupId;
    }

    /**
     * @param string $itemGroupId
     * @return FacebookProduct
     */
    public function setItemGroupId($itemGroupId)
    {
        $this->itemGroupId = $itemGroupId;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleProductCategory()
    {
        return $this->googleProductCategory;
    }

    /**
     * @param string $googleProductCategory
     * @return FacebookProduct
     */
    public function setGoogleProductCategory($googleProductCategory)
    {
        $this->googleProductCategory = $googleProductCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommerceTaxCategory()
    {
        return $this->commerceTaxCategory;
    }

    /**
     * @param string $commerceTaxCategory
     * @return FacebookProduct
     */
    public function setCommerceTaxCategory($commerceTaxCategory)
    {
        $this->commerceTaxCategory = $commerceTaxCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param string $material
     * @return FacebookProduct
     */
    public function setMaterial($material)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @return FacebookProduct
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @param string $productType
     * @return FacebookProduct
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    /**
     * @param string $salePrice
     * @return FacebookProduct
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalePriceEffectiveDate()
    {
        return $this->salePriceEffectiveDate;
    }

    /**
     * @param string $salePriceEffectiveDate
     * @return FacebookProduct
     */
    public function setSalePriceEffectiveDate($salePriceEffectiveDate)
    {
        $this->salePriceEffectiveDate = $salePriceEffectiveDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param string $shipping
     * @return FacebookProduct
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingWeight()
    {
        return $this->shippingWeight;
    }

    /**
     * @param string $shippingWeight
     * @return FacebookProduct
     */
    public function setShippingWeight($shippingWeight)
    {
        $this->shippingWeight = $shippingWeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     * @return FacebookProduct
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomLabel0()
    {
        return $this->customLabel0;
    }

    /**
     * @param string $customLabel0
     * @return FacebookProduct
     */
    public function setCustomLabel0($customLabel0)
    {
        $this->customLabel0 = $customLabel0;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomLabel1()
    {
        return $this->customLabel1;
    }

    /**
     * @param string $customLabel1
     * @return FacebookProduct
     */
    public function setCustomLabel1($customLabel1)
    {
        $this->customLabel1 = $customLabel1;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomLabel2()
    {
        return $this->customLabel2;
    }

    /**
     * @param string $customLabel2
     * @return FacebookProduct
     */
    public function setCustomLabel2($customLabel2)
    {
        $this->customLabel2 = $customLabel2;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomLabel3()
    {
        return $this->customLabel3;
    }

    /**
     * @param string $customLabel3
     * @return FacebookProduct
     */
    public function setCustomLabel3($customLabel3)
    {
        $this->customLabel3 = $customLabel3;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomLabel4()
    {
        return $this->customLabel4;
    }

    /**
     * @param string $customLabel4
     * @return FacebookProduct
     */
    public function setCustomLabel4($customLabel4)
    {
        $this->customLabel4 = $customLabel4;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichTextDescription()
    {
        return $this->richTextDescription;
    }

    /**
     * @param string $richTextDescription
     * @return FacebookProduct
     */
    public function setRichTextDescription($richTextDescription)
    {
        $this->richTextDescription = $richTextDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getGtin()
    {
        return $this->gtin;
    }

    /**
     * @param string $gtin
     * @return FacebookProduct
     */
    public function setGtin($gtin)
    {
        $this->gtin = $gtin;

        return $this;
    }

    /**
     * @return string
     */
    public function getMpn()
    {
        return $this->mpn;
    }

    /**
     * @param string $mpn
     * @return FacebookProduct
     */
    public function setMpn($mpn)
    {
        $this->mpn = $mpn;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnPolicyInfo()
    {
        return $this->returnPolicyInfo;
    }

    /**
     * @param string $returnPolicyInfo
     * @return FacebookProduct
     */
    public function setReturnPolicyInfo($returnPolicyInfo)
    {
        $this->returnPolicyInfo = $returnPolicyInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getLaunchDate()
    {
        return $this->launchDate;
    }

    /**
     * @param string $launchDate
     * @return FacebookProduct
     */
    public function setLaunchDate($launchDate)
    {
        $this->launchDate = $launchDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param string $expirationDate
     * @return FacebookProduct
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     * @return FacebookProduct
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @return string
     */
    public function getMobileLink()
    {
        return $this->mobileLink;
    }

    /**
     * @param string $mobileLink
     * @return FacebookProduct
     */
    public function setMobileLink($mobileLink)
    {
        $this->mobileLink = $mobileLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalVariantAttribute()
    {
        return $this->additionalVariantAttribute;
    }

    /**
     * @param string $additionalVariantAttribute
     * @return FacebookProduct
     */
    public function setAdditionalVariantAttribute($additionalVariantAttribute)
    {
        $this->additionalVariantAttribute = $additionalVariantAttribute;

        return $this;
    }

    /**
     * @return string
     */
    public function getAppLink()
    {
        return $this->appLink;
    }

    /**
     * @param string $appLink
     * @return FacebookProduct
     */
    public function setAppLink($appLink)
    {
        $this->appLink = $appLink;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [];
    }
}
