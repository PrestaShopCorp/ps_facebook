<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO\Object;

use JsonSerializable;

class Page implements JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $page;

    /**
     * @var int|null
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
     * @param int|null $likes
     * @param string|null $logo
     */
    public function __construct($id, $page, $likes, $logo)
    {
        $this->id = $id;
        $this->page = $page;
        $this->likes = $likes;
        $this->logo = $logo;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int|null
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
            'id' => $this->getId(),
            'page' => $this->getPage(),
            'likes' => $this->getLikes(),
            'logo' => $this->getLogo(),
        ];
    }
}
