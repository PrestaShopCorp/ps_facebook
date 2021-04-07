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

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use FacebookAds\Object\ServerSide\Normalizer;
use FacebookAds\Object\ServerSide\Util;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class PixelHandler
{
    /**
     * @var \Context
     */
    private $context;

    /**
     * @var \Ps_facebook
     */
    private $module;

    /**
     * @var TemplateBuffer
     */
    private $templateBuffer;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    public function __construct($module, ConfigurationAdapter $configurationAdapter)
    {
        $this->context = \Context::getContext();
        $this->module = $module;
        $this->templateBuffer = $module->templateBuffer;
        $this->configurationAdapter = $configurationAdapter;
    }

    public function handleEvent($params)
    {
        $pixel_id = $this->configurationAdapter->get(Config::PS_PIXEL_ID);
        if (empty($pixel_id)) {
            return;
        }
        $track = 'track';

        $eventType = false;
        if (isset($params['event_type'])) {
            $eventType = $params['event_type'];
        }
        if (isset($params['user'])) {
            $userData = $params['user'];
        }

        $content = $eventData = [];
        if (isset($params['eventID'])) {
            $eventData = ['eventID' => $params['eventID']];
        }
        if (isset($params['custom_data'])) {
            $content = $params['custom_data'];
        }

        $smartyVariables = [
            'pixel_fc' => $this->module->front_controller,
            'id_pixel' => $pixel_id,
            'type' => $eventType,
            'content' => $this->formatPixel($content),
            'track' => $track,
            'eventData' => $this->formatPixel($eventData),
        ];

        if (isset($userData)) {
            $smartyVariables['userInfos'] = $this->getCustomerInformation($userData);
        }

        $this->context->smarty->assign($smartyVariables);

        $this->templateBuffer->add($this->module->display($this->module->getfilePath(), '/views/templates/hook/header.tpl'));
    }

    /**
     * formatPixel
     *
     * @param array $params
     *
     * @return string|false
     */
    protected function formatPixel($params)
    {
        return json_encode((object) $params);
    }

    /**
     * getCustomerInformation
     *
     * @param array $customerInformation
     *
     * @return array
     */
    protected function getCustomerInformation($customerInformation)
    {
        return [
            'ct' => Util::hash(Normalizer::normalize('ct', $customerInformation['city'])),
            'country' => Util::hash(Normalizer::normalize('country', $customerInformation['countryIso'])),
            'zp' => Util::hash(Normalizer::normalize('zp', $customerInformation['postCode'])),
            'ph' => Util::hash(Normalizer::normalize('ph', $customerInformation['phone'])),
            'gender' => Util::hash(Normalizer::normalize('gender', $customerInformation['gender'])),
            'fn' => Util::hash(Normalizer::normalize('fn', $customerInformation['firstname'])),
            'ln' => Util::hash(Normalizer::normalize('ln', $customerInformation['lastname'])),
            'em' => Util::hash(Normalizer::normalize('em', $customerInformation['email'])),
            'bd' => Util::hash(Normalizer::normalize('bd', $customerInformation['birthday'])),
            'st' => Util::hash(Normalizer::normalize('st', $customerInformation['stateIso'])),
        ];
    }
}
