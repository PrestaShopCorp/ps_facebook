<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

use JsonSerializable;

class Pixel implements JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $lastActive;

    /**
     * @var bool
     */
    private $isUnavailable;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * Pixel constructor.
     *
     * @param string $name
     * @param string $id
     * @param string $lastActive
     * @param bool $isUnavailable
     * @param bool $isActive
     */
    public function __construct($name, $id, $lastActive, $isUnavailable, $isActive)
    {
        $this->name = $name;
        $this->id = $id;
        $this->lastActive = $lastActive;
        $this->isUnavailable = $isUnavailable;
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
    public function getLastActive()
    {
        return $this->lastActive;
    }

    /**
     * @return bool
     */
    public function isUnavailable()
    {
        return $this->isUnavailable;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'id' => $this->getId(),
            'lastActive' => $this->getLastActive(),
            'isUnavailable' => $this->isUnavailable(),
            'isActive' => $this->isActive(),
        ];
    }
}
