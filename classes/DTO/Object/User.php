<?php

namespace PrestaShop\Module\PrestashopFacebook\DTO\Object;

use JsonSerializable;

class user implements JsonSerializable
{
    /**
     * @var string
     */
    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function jsonSerialize()
    {
        return [
            'email' => $this->getEmail(),
        ];
    }
}
