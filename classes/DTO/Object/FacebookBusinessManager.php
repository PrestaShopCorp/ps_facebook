<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO;

use JsonSerializable;

class FacebookBusinessManager implements JsonSerializable
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
    private $mail;

    /**
     * @var int
     */
    private $createdAt;

    /**
     * FacebookBusinessManager constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $mail
     * @param int $createdAt
     */
    public function __construct($id, $name, $mail, $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->mail = $mail;
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
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @return int
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
            'mail' => $this->getMail(),
            'createDate' => $this->getCreatedAt(),
        ];
    }
}
