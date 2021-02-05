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

class Catalog implements JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $productSyncStarted;

    /**
     * @var bool
     */
    private $categoryMatchingStarted;

    /**
     * Page constructor.
     *
     * @param string $id
     */
    public function __construct($id, $productSyncStarted, $categoryMatchingStarted)
    {
        $this->id = $id;
        $this->productSyncStarted = $productSyncStarted;
        $this->categoryMatchingStarted = $categoryMatchingStarted;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getProductSyncStarted()
    {
        return $this->productSyncStarted;
    }

    /**
     * @return bool
     */
    public function getCategoryMatchingStarted()
    {
        return $this->categoryMatchingStarted;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'productSyncStarted' => $this->getProductSyncStarted(),
            'categoryMatchingStarted' => $this->getCategoryMatchingStarted(),
        ];
    }
}
