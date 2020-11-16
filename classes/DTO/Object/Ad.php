<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

use JsonSerializable;

class Ad implements JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @param string $name
     * @param string $email
     * @param string $createdAt
     */
    public function __construct($id, $name, $email, $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = $createdAt;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function jsonSerialize()
    {
        return [
        'id' => $this->getId(),
          'name' => $this->getName(),
          'email' => $this->getEmail(),
          'createdAt' => $this->getCreatedAt(),
        ];
    }
}
