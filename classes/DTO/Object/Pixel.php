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
     * @var int
     */
    private $lastActive;

    /**
     * @var bool
     */
    private $activated;

    /**
     * Pixel constructor.
     *
     * @param string $name
     * @param string $id
     * @param int $lastActive
     * @param bool $activated
     */
    public function __construct($name, $id, $lastActive, $activated)
    {
        $this->name = $name;
        $this->id = $id;
        $this->lastActive = $lastActive;
        $this->activated = $activated;
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
     * @return int
     */
    public function getLastActive()
    {
        return $this->lastActive;
    }

    /**
     * @return bool
     */
    public function isActivated()
    {
        return $this->activated;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'id' => $this->getId(),
            'lastActive' => $this->getLastActive(),
            'isActive' => $this->isActivated(),
        ];
    }
}
