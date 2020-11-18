<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;

abstract class BaseEvent
{
    /**
     * @var \Context
     */
    protected $context;

    /**
     * @var \Ps_facebook
     */
    protected $module;

    public function __construct(\Context $context, \Ps_facebook $module)
    {
        $this->context = $context;
        $this->module = $module;
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
