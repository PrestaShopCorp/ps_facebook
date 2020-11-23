<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

use JsonSerializable;
use PrestaShop\Module\PrestashopFacebook\DTO\Object\user;

class ContextPsFacebook implements JsonSerializable
{
    /**
     * @var User
     */
    private $user;

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
     * @var Ad|null
     */
    private $ad;

    /**
     * @var bool|null
     */
    private $categoriesMatching;

    /**
     * ContextPsFacebook constructor.
     *
     * @param User $user
     * @param FacebookBusinessManager|null $facebookBusinessManager
     * @param Pixel|null $pixel
     * @param Page|null $page
     * @param Ad|null $ad
     * @param bool|null $categoriesMatching
     */
    public function __construct($user, $facebookBusinessManager, $pixel, $page, $ad, $categoriesMatching, $catalogId)
    {
        $this->user = $user;
        $this->facebookBusinessManager = $facebookBusinessManager;
        $this->pixel = $pixel;
        $this->page = $page;
        $this->ad = $ad;
        $this->categoriesMatching = $categoriesMatching;
        $this->catalogId = $catalogId;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return ContextPsFacebook
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
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
     * @return Ad|null
     */
    public function getAd()
    {
        return $this->ad;
    }

    /**
     * @param Ad|null $ad
     *
     * @return ContextPsFacebook
     */
    public function setAd($ad)
    {
        $this->ad = $ad;

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

    /**
     * @return string|null
     */
    public function getCatalogId()
    {
        return $this->catalogId;
    }

    /**
     * @param string|null $catalogId
     *
     * @return ContextPsFacebook
     */
    public function setCatalogId($catalogId)
    {
        $this->catalogId = $catalogId;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'user' => $this->getUser(),
            'pixel' => $this->getPixel(),
            'facebookBusinessManager' => $this->getFacebookBusinessManager(),
            'page' => $this->getPage(),
            'ads' => $this->getAd(),
            'categoriesMatching' => $this->getCategoriesMatching(),
            'catalogId' => $this->getCatalogId(),
        ];
    }
}
