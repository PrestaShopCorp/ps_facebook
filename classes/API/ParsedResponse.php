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

namespace PrestaShop\Module\PrestashopFacebook\API;

use Psr\Http\Message\ResponseInterface;

class ParsedResponse
{
    /**
     * @var ResponseInterface
     */
    private $response;
    private $body;

    public function __construct(ResponseInterface $response)
    {
        $this->body = json_decode($response->getBody()->getContents(), true);
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function isSuccessful(): bool
    {
        return $this->responseIsSuccessful($this->response->getStatusCode());
    }

    public function toArray(): array
    {
        $responseContents = $this->body;

        return [
            'isSuccessful' => $this->isSuccessful(),
            'httpCode' => $this->response->getStatusCode(),
            'body' => $responseContents,
        ];
    }

    /**
     * Check if the response is successful or not (response code 200 to 299).
     *
     * @param int $httpStatusCode
     *
     * @return bool
     */
    private function responseIsSuccessful($httpStatusCode)
    {
        return '2' === substr((string) $httpStatusCode, 0, 1);
    }
}
