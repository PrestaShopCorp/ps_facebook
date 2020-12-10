<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

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

    /**
     * Pixel constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $lastActive
     * @param bool $isUnavailable
     * @param bool $isActive
     */
    public function __construct($id, $name, $lastActive, $isUnavailable, $isActive)
    {
        $this->id = $id;
        $this->name = $name;
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'lastActive' => $this->getLastActive(),
            'isUnavailable' => $this->isUnavailable(),
            'isActive' => $this->isActive(),
        ];
    }
}
