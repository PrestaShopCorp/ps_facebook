<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

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

    public function __construct($module)
    {
        $this->context = \Context::getContext();
        $this->module = $module;
        $this->templateBuffer = $module->templateBuffer;
    }

    public function handleEvent($params)
    {
        $pixel_id = \Configuration::get(Config::PS_PIXEL_ID);
        if (empty($pixel_id)) {
            return;
        }
        $track = 'track';

        if (isset($params['event_type'])) {
            $eventType = $params['event_type'];
        }
        if (isset($params['user'])) {
            $userData = $params['user'];
        }

        $content = [];
        if (isset($params['custom_data'])) {
            $customData = $params['custom_data'];
            $content = $this->formatPixel($customData);
        }


        $smartyVariables = [
            'pixel_fc' => $this->module->front_controller,
            'id_pixel' => $pixel_id,
            'type' => $eventType,
            'content' => $content,
            'track' => $track,
        ];

        if ($this->context->customer->id) {
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
        return json_encode($params);
    }

    /**
     * getCustomerInformations
     *
     * @param $customerInformation
     *
     * @return array
     */
    protected function getCustomerInformation($customerInformation)
    {
        return [
            'ct' => $customerInformation['city'],
            'country' => $customerInformation['countryIso'],
            'zp' => $customerInformation['postCode'],
            'ph' => $customerInformation['phone'],
            'gender' => $customerInformation['gender'],
            'fn' => $customerInformation['firstname'],
            'ln' => $customerInformation['lastname'],
            'em' => $customerInformation['email'],
            'bd' => $customerInformation['birthday'],
            'st' => $customerInformation['stateIso'],
        ];
    }
}
