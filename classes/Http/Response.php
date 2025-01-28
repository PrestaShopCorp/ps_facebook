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

declare(strict_types=1);

namespace PrestaShop\Module\PrestashopFacebook\Domain\Http;

class Response
{
    /** @var int */
    private $statusCode;
    /** @var string */
    private $body;
    /** @var array<string,string> */
    private $headers;

    /**
     * @param int $statusCode
     * @param string $body
     * @param array<string,string> $headers
     **/
    public function __construct($statusCode, $body, $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array<string,string>
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($headerName)
    {
        return $this->headers[$headerName] ?? null;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return json_decode($this->body, true);
    }

    public function isSuccessful()
    {
        return substr((string) $this->statusCode, 0, 1) == '2';
    }
}
