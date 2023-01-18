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

namespace PrestaShop\Module\PrestashopFacebook\Dispatcher;

use Context;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Handler\ApiConversionHandler;
use PrestaShop\Module\PrestashopFacebook\Handler\PixelHandler;
use PrestaShop\Module\PrestashopFacebook\Provider\EventDataProvider;

class EventDispatcher
{
    /**
     * @var ApiConversionHandler
     */
    private $conversionHandler;

    /**
     * @var PixelHandler
     */
    private $pixelHandler;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var EventDataProvider
     */
    private $eventDataProvider;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        ApiConversionHandler $apiConversionHandler,
        PixelHandler $pixelHandler,
        ConfigurationAdapter $configurationAdapter,
        EventDataProvider $eventDataProvider,
        Context $context
    ) {
        $this->conversionHandler = $apiConversionHandler;
        $this->pixelHandler = $pixelHandler;
        $this->configurationAdapter = $configurationAdapter;
        $this->eventDataProvider = $eventDataProvider;
        $this->context = $context;
    }

    /**
     * @param string $name
     * @param array $params
     *
     * @return string
     */
    public function dispatch($name, array $params)
    {
        // Events are related to actions on the shop, not the back office
        /** @var \Controller|null $controller */
        $controller = $this->context->controller;
        if (!$controller || !in_array($controller->controller_type, ['front', 'modulefront'])) {
            return '';
        }

        if (false === (bool) $this->configurationAdapter->get(Config::PS_FACEBOOK_PIXEL_ENABLED)) {
            return '';
        }

        $eventData = $this->eventDataProvider->generateEventData($name, $params);

        if ($eventData) {
            $this->conversionHandler->handleEvent($eventData);
        }

        return $this->pixelHandler->handleEvent($eventData, $name);
    }
}
