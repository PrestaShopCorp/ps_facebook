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

use Context;
use FacebookAds\Object\ServerSide\Normalizer;
use FacebookAds\Object\ServerSide\Util;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class PixelHandler
{
    /**
     * @var Context
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
        $this->context = Context::getContext();
        $this->module = $module;
        $this->templateBuffer = $module->templateBuffer;
        $this->configurationAdapter = $configurationAdapter;

        $this->templateBuffer->init($this->findIdentifierFromContext($this->context));
    }

    /**
     * @param array|bool $params
     * @param string $hookName
     *
     * @return string
     */
    public function handleEvent($params, $hookName)
    {
        $pixel_id = $this->configurationAdapter->get(Config::PS_PIXEL_ID);
        if (empty($pixel_id)) {
            return '';
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

        $content = '';

        if ($hookName === 'hookDisplayHeader') {
            $this->context->smarty->assign($smartyVariables);

            $content .= $this->module->display($this->module->getfilePath(), 'views/templates/hook/header.tpl');
        }

        $this->context->smarty->assign($smartyVariables);
        $this->templateBuffer->add($this->module->display($this->module->getfilePath(), 'views/templates/hook/fbTrack.tpl'));

        // Return the existing content in case we have a display hook
        if (strpos($hookName, 'Display') === 4 && !$this->isCurrentRequestAnAjax()) {
            $content .= $this->templateBuffer->flush();
        }

        return $content;
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

    /**
     * @return bool
     */
    private function isCurrentRequestAnAjax()
    {
        /*
         * An ajax property is available in controllers
         * preventing the whole page template to be generated.
         */
        if ($this->context->controller->ajax) {
            return true;
        }

        /*
         * In case the ajax property is not properly set, there is
         * another check available.
         */
        if ($this->context->controller->isXmlHttpRequest()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    private function findIdentifierFromContext(Context $context)
    {
        if (!empty($context->customer->id_guest)) {
            return 'guest_' . $context->customer->id_guest;
        }
        if (!empty($context->cart->id)) {
            return 'cart_' . $context->cart->id;
        }

        return '';
    }
}
