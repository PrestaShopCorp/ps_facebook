<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO\Object;

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

    private $settingsUrl;

    /**
     * Pixel constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $lastActive
     * @param bool $isUnavailable
     * @param bool $isActive
     * @param $settingsUrl
     */
    public function __construct($id, $name, $lastActive, $isUnavailable, $isActive, $settingsUrl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastActive = $lastActive;
        $this->isUnavailable = $isUnavailable;
        $this->isActive = $isActive;
        $this->settingsUrl = $settingsUrl;
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

    /**
     * @return mixed
     */
    public function getSettingsUrl()
    {
        return $this->settingsUrl;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'lastActive' => $this->getLastActive(),
            'isUnavailable' => $this->isUnavailable(),
            'isActive' => $this->isActive(),
            'settingsUrl' => $this->getSettingsUrl(),
        ];
    }
}
