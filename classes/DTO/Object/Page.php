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
