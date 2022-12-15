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

namespace PrestaShop\Module\PrestashopFacebook\API\EventSubscriber;

use Exception;
use PrestaShop\Module\PrestashopFacebook\API\ParsedResponse;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;

class ApiErrorSubscriber implements SubscriberInterface
{
    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function onParsedResponse(ParsedResponse $response, array $options): void
    {
        if ($response->isSuccessful()) {
            return;
        }

        $class = $options['exceptionClass'] ?: Exception::class;

        // TODO: Error sent to the error handler can be improved from the response content
        $this->errorHandler->handle(
            new $class(
                $this->getMessage($response)
            ),
            $response->getResponse()->getStatusCode(),
            false,
            [
                'extra' => $response->getBody(),
            ]
        );
    }

    private function getMessage(ParsedResponse $response)
    {
        $body = $response->getBody();
        // If there is a error object returned by the Facebook API, use their codes
        if (!empty($body['error']['code']) && !empty($body['error']['error_subcode']) && !empty($body['error']['type'])) {
            return 'Facebook API errored with ' . $body['error']['type'] . ' (' . $body['error']['code'] . ' / ' . $body['error']['error_subcode'] . ')';
        }
        if (!empty($body['error']['code']) && !empty($body['error']['type'])) {
            return 'Facebook API errored with ' . $body['error']['type'] . ' (' . $body['error']['code'] . ')';
        }

        return 'API errored with HTTP ' . $response->getResponse()->getStatusCode();
    }
}
