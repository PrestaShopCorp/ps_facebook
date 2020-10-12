<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

use JsonSerializable;

class ContextPsFacebook implements JsonSerializable
{
    /**
     * @var FacebookBusinessManager|null
     */
    private $facebookBusinessManager;

    /**
     * @var Pixel|null
     */
    private $pixel;

    /**
     * @var Page|null
     */
    private $page;

    /**
     * @var Ads|null
     */
    private $ads;

    /**
     * @var bool|null
     */
    private $categoriesMatching;

    /**
     * ContextPsFacebook constructor.
     *
     * @param FacebookBusinessManager|null $facebookBusinessManager
     * @param Pixel|null $pixel
     * @param Page[]|null $page
     * @param Ads|null $ads
     * @param bool|null $categoriesMatching
     */
    public function __construct($facebookBusinessManager, $pixel, $page, $ads, $categoriesMatching)
    {
        $this->facebookBusinessManager = $facebookBusinessManager;
        $this->pixel = $pixel;
        $this->page = $page;
        $this->ads = $ads;
        $this->categoriesMatching = $categoriesMatching;
    }

    /**
     * @return FacebookBusinessManager|null
     */
    public function getFacebookBusinessManager()
    {
        return $this->facebookBusinessManager;
    }

    /**
     * @param FacebookBusinessManager|null $facebookBusinessManager
     *
     * @return ContextPsFacebook
     */
    public function setFacebookBusinessManager($facebookBusinessManager)
    {
        $this->facebookBusinessManager = $facebookBusinessManager;

        return $this;
    }

    /**
     * @return Pixel|null
     */
    public function getPixel()
    {
        return $this->pixel;
    }

    /**
     * @param Pixel|null $pixel
     *
     * @return ContextPsFacebook
     */
    public function setPixel($pixel)
    {
        $this->pixel = $pixel;

        return $this;
    }

    /**
     * @return Page|null
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page|null $page
     *
     * @return ContextPsFacebook
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return Ads|null
     */
    public function getAds()
    {
        return $this->ads;
    }

    /**
     * @param Ads|null $ads
     *
     * @return ContextPsFacebook
     */
    public function setAds($ads)
    {
        $this->ads = $ads;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCategoriesMatching()
    {
        return $this->categoriesMatching;
    }

    /**
     * @param bool|null $categoriesMatching
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
            'pixel' => $this->getPixel(),
            'page' => $this->getPage(),
            'ads' => $this->getAds(),
            'categoriesMatching' => $this->getCategoriesMatching(),
        ];
    }
}
