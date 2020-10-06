<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

use JsonSerializable;

class Page implements JsonSerializable
{
    /**
     * @var string
     */
    private $page;

    /**
     * @var int
     */
    private $likes;

    /**
     * @var string|null
     */
    private $logo;

    /**
     * Page constructor.
     *
     * @param string $page
     * @param int $likes
     * @param string|null $logo
     */
    public function __construct($page, $likes, $logo)
    {
        $this->page = $page;
        $this->likes = $likes;
        $this->logo = $logo;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @return string|null
     */
    public function getLogo()
    {
        return $this->logo;
    }

    public function jsonSerialize()
    {
        return [
            'page' => $this->getPage(),
            'likes' => $this->getLikes(),
            'logo' => $this->getLogo(),
        ];
    }
}
