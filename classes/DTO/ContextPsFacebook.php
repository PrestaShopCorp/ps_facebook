<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

use JsonSerializable;

class ContextPsFacebook implements JsonSerializable
{
    /**
     * @var Pixel
     */
    private $pixel;

    /**
     * @var Page
     */
    private $page;

    /**
     * @var Ads
     */
    private $ads;

    /**
     * @var bool
     */
    private $categoriesMatching;

    /**
     * ContextPsFacebook constructor.
     *
     * @param Pixel $pixel
     * @param Page $page
     * @param Ads $ads
     * @param bool $categoriesMatching
     */
    public function __construct(Pixel $pixel, Page $page, Ads $ads, $categoriesMatching)
    {
        $this->pixel = $pixel;
        $this->page = $page;
        $this->ads = $ads;
        $this->categoriesMatching = $categoriesMatching;
    }

    /**
     * @return Pixel
     */
    public function getPixel()
    {
        return $this->pixel;
    }

    /**
     * @param Pixel $pixel
     *
     * @return ContextPsFacebook
     */
    public function setPixel($pixel)
    {
        $this->pixel = $pixel;

        return $this;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     *
     * @return ContextPsFacebook
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return Ads
     */
    public function getAds()
    {
        return $this->ads;
    }

    /**
     * @param Ads $ads
     *
     * @return ContextPsFacebook
     */
    public function setAds($ads)
    {
        $this->ads = $ads;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCategoriesMatching()
    {
        return $this->categoriesMatching;
    }

    /**
     * @param bool $categoriesMatching
     *
     * @return ContextPsFacebook
     */
    public function setCategoriesMatching($categoriesMatching)
    {
        $this->categoriesMatching = $categoriesMatching;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'pixel' => $this->getPixel()->jsonSerialize(),
            'page' => $this->getPage()->jsonSerialize(),
            'ads' => $this->getAds()->jsonSerialize(),
            'categoriesMatching' => $this->isCategoriesMatching(),
        ];
    }
}
